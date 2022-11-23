<template>
    <div>
        <div class="revision-selector ct-tool ct-tool--clock" :class="{'ct-tool--disabled': !active, 'ct-tool--applied': editMode}" @click="setEditMode()"></div>
        <transition name="fade">
            <div class="selector" v-if="editMode">
                <ul class="uk-nav uk-nav-default revision-list uk-text-right" >
                    <li :class="{'uk-active': setVersion === -1}" @click="setRevision(-1)">{{translations.current}}</li>
                    <li :class="{'uk-active': setVersion === index}" v-for="(revision, index) in revisions" @click="setRevision(index)">
                        {{revision.date}}
                    </li>
                </ul>
            </div>
        </transition>
    </div>
</template>

<script>
    export default {
        name: 'RevisionContainer',
        data() {
            return {
                file: '',
                component: '',
                currentRegion: null,
                editMode: false,
                active: false,
                hasRevision: false,
                currentVersion: '',
                setVersion: -1,
                revisions: [],
                initialized: false,
                isFixture: null,
                translations: document.contentEditorTranslations
            }
        },
        mounted() {
        },
        methods: {
            setEditMode(state) {
                if (!this.active) return;
                state = state === undefined ? !this.editMode : state;
                if (state) {
                    this.editMode = true;
                    this.getRevisions();
                } else {
                    this.editMode = false;
                }
            },
            setData(file, component) {
                this.file = file;
                this.component = component;
            },
            getRevisions() {
                $.request(
                    this.component,
                    {data: {file: this.file}, success: data => {
                        setTimeout(() => {
                            const emptyElements = document.querySelector(`*[data-file="${this.file}"] .ce-element--empty`);
                            if (emptyElements) {
                                emptyElements.remove();
                            }
                            const oldContent = ContentTools.EditorApp.get().regions();
                            if (oldContent[this.file]) {
                                this.currentRegion = oldContent[this.file];
                                this.currentVersion = this.currentRegion.html();
                                if (this.isFixture) {
                                    this.currentVersion = $(this.currentVersion)[0].innerHTML.trim();
                                }
                            } else {
                                this.hasRevision = false;
                            }
                        }, 200);
                        this.revisions = data.filter(revision => revision.old_value !== null);
                        this.hasRevision = this.revisions.length > 0;
                    }}
                );
            },
            setRevision(index) {
                const newContent = index === -1 ? this.currentVersion : this.revisions[index].old_value;
                if (this.isFixture) {
                    document.querySelector(`*[data-file="${this.file}"]`).innerHTML = newContent;
                } else {
                    this.currentRegion.setContent(newContent);
                }
                setTimeout(() => {
                    const emptyElements = document.querySelector(`*[data-file="${this.file}"] .ce-element--empty`);
                    if (emptyElements) {
                        emptyElements.remove();
                    }
                    setTimeout(() => {
                        const emptyElements = document.querySelector(`*[data-file="${this.file}"] .ce-element--empty`);
                        if (emptyElements) {
                            emptyElements.remove();
                        }
                    }, 1500);
                });
                this.setVersion = index;
            },
            undo() {
                this.setRevision(-1);
                this.setEditMode(false);
            }
        }
    }
</script>
<style lang="scss">
    .selector {
        position: absolute;
        width: 170px;
        left: -170px;
        bottom: 0;
        background: rgba(#fff, .9);
        border: solid 1px #999;
        box-shadow: -5px 5px 10px rgba(0, 0, 0, 0.2) !important;
        .revision-list {
            padding: 20px 10px;
            li {
                padding: 2px 10px;
                color: #222 !important;
                text-align: left;
            }
        }
        li {
            &.uk-active {
                font-weight: bold;
            }
        }
    }
</style>
