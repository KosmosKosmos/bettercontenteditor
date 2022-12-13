import Vue from 'vue$';
import UIkit from 'uikit';
import './contenteditor.scss';
import './samuell.js';

Vue.config.shadowRoot = true;
Vue.config.devtools = false;
Vue.config.productionTip = false;

new Vue({
    delimiters: ['${', '}'],
    data() {
        return {saving: false, visible: true, history: null, revision: null}
    },
    mounted() {
        let editor = ContentTools.EditorApp.get();
        editor.addEventListener('revert', e => {
            e.preventDefault();
            this.askToClose();
            return false;
        });

        editor.addEventListener('saved', ev => {
            editor.busy(true);
            const regions = ev.detail().regions;
            for (let name in regions) {
                if (regions.hasOwnProperty(name)) {
                    const content = document.querySelector('*[data-file="' + name + '"]');
                    const isFixture = content.hasAttribute('data-fixture');
                    const htmlData = isFixture ? content.innerHTML.trim() : regions[name];
                    $.request(
                        content.getAttribute('data-component'),
                        {data: {file: name, content: htmlData}}
                    );
                }
            }

            UIkit.notification(document.contentEditorTranslations.changesSaved);
            setTimeout(function () {
                editor.busy(false);
            }, 600);
        });

        window.addEventListener("keyup", e => {
            if (e.key === "Escape") {
                if (editor._ignition.state() === 'ready') {
                    if (this.visible) {
                        editor._ignition.hide();
                    } else {
                        editor._ignition.show();
                    }
                    this.visible = !this.visible;
                } else {
                    this.askToClose();
                }
            }
        });

        window.addEventListener("keydown", e => {
            if (editor._ignition.state() !== 'ready') {
                if ((e.metaKey || e.ctrlKey) && e.key === "s") {
                    e.preventDefault();
                    try {
                        editor.stop(true);
                    } catch (e) {
                        console.log(e);
                    }
                    editor._ignition.state("ready");
                    editor.dispatchEvent(editor.createEvent('stopped'));
                }
            }
        });

        window.addEventListener("keydown", e => {
            if ((e.metaKey || e.ctrlKey) && e.key === "e") {
                e.preventDefault();
                editor._ignition.edit();
                editor._ignition.show();
            }
        });

        setTimeout(() => {
            ContentEdit.Root.get().bind('focus', element => {
                const dataset = element._parent._domElement.dataset;
                if (dataset.getrevisions) {
                    this.revision.$children[0].editMode = false;
                    this.revision.$children[0].file = dataset.file;
                    this.revision.$children[0].component = dataset.getrevisions;
                    this.revision.$children[0].isFixture = dataset.fixture !== undefined;
                    this.revision.$children[0].active = true;
                } else {
                    this.revision.$children[0].active = false;
                }
            });
        }, 1000);
    },
    methods: {
        askToClose() {
            if (editor._rootLastModified && ContentEdit.Root.get().lastModified() > editor._rootLastModified) {
                UIkit.modal.confirm(
                    document.contentEditorTranslations.changesLost, {
                        labels: {
                            ok: document.contentEditorTranslations.yes,
                            cancel: document.contentEditorTranslations.no
                        }
                    }).then(() => {
                    this.revert();
                }, () => {});
            } else {
                this.revert();
            }
        },
        revert() {
            if (editor.history) {
                editor.revertToSnapshot(editor.history.goTo(0), false);
            }
            this.close();
        },
        close() {
            if (editor.history) {
                editor.history.stopWatching();
                editor.history = null;
            }
            editor._toolbox.hide();
            editor._inspector.hide();
            editor._regions = {};
            editor._state = 'ready';
            if (ContentEdit.Root.get().focused()) {
                editor._allowEmptyRegions((function(_this) {
                    return function() {
                        return ContentEdit.Root.get().focused().blur();
                    };
                })(editor));
            }
            editor._ignition.state("ready");
            editor.dispatchEvent(editor.createEvent('stopped'));
        },
        elementSelected(file) {
            console.log(file);
        }
    }
}).$mount('#content-editor-app');
