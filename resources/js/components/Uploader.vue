<template>
    <div class="container">
        <div v-if="!file">
            <div class="text-center">
                <h2>Sign your pdf document online</h2>
            </div>

            <div class="alert alert-danger" role="alert" v-if="error">
                <strong>{{this.error}}</strong>
                <button type="button" class="close" aria-label="Close" @click="error = ''">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div :class="{'box': true, 'has-advanced-upload': canDrag, 'is-dragover': isDragging}"
                 ref="form" method="post" action="" enctype="multipart/form-data" @drop="onDrop"
                 @drag="prevent" @dragstart="prevent" @dragend="onDragLeave" @dragover="onDragOver"
                 @dragenter="onDragOver" @dragleave="onDragLeave">
                <div class="d-flex justify-content-center">
                    <input type="file" accept="application/pdf" id="file" hidden @change="onSelectFile" />
                    <label for="file"><strong class="choose-file">Choose a file</strong><span v-if="canDrag"> or drag it here</span>.</label>
                </div>
            </div>
        </div>

        <ElectricSignatureSettingArea v-else ref="modal" :pdfData="file" @reupload="reset"
                                      @onSucceed="onSucceedCreateSignaturePosition" />
    </div>
</template>

<script>
    import FileUtility from '../utility/FileUtility';
    import UploadService from "../services/UploadService";
    import ElectricSignatureSettingArea from "./ElectricSignatureSettingArea";

    const hasAdvancedUpload = function() {
        const div = document.createElement('div');
        return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
    }();

    export default {
        name: "Uploader",
        components: {ElectricSignatureSettingArea},
        data() {
            return {
                canDrag: hasAdvancedUpload,
                isDragging: false,
                file: null,
                error: ''
            }
        },
        mounted() {

        },
        methods: {
            prevent(e) {
                e.preventDefault();
                e.stopPropagation();
            },
            onDragOver(e) {
                this.prevent(e);
                this.isDragging = true;
            },
            onDragLeave(e) {
                this.prevent(e);
                this.isDragging = false;
            },
            onDrop(e) {
                this.onDragLeave(e);
                this.validateAndLoadPdf(e.dataTransfer.files[0]);
            },
            onSelectFile(e) {
                this.validateAndLoadPdf(e.target.files[0]);
            },
            validateAndLoadPdf(file) {
                if(file.type === 'application/pdf') {
                    const fileSizeMB = file.size / 1024 / 1024;
                    if(fileSizeMB > 2) {
                        this.error = 'Your pdf file cannot greater than 2 MB';
                    } else {
                        FileUtility.getBase64(file).then(base64 => {
                            this.file = base64;
                            this.error = '';
                        });
                    }
                } else {
                    this.error = 'Please select a pdf file';
                }
            },
            onSucceedCreateSignaturePosition(data) {
                const signaturePosition = data['signaturePosition'];
                const pdfWidth = data['pdfWidth'];
                const pdfHeight = data['pdfHeight'];
                const numPages = data['numPages'];
                const title = data['title'];
                UploadService.uploadDocument(title, this.file, signaturePosition['id'], pdfWidth, pdfHeight, numPages).then(document => {
                    this.$swal('Succeed!', 'You created a document successfully', 'success');
                    setTimeout(() => {
                        window.location = '/signer/' + document['data']['id'];
                    }, 1000);
                }).catch(reason => {
                    console.log(reason);
                    this.$swal('Failed!', 'Error occurs when you create a document', 'error');
                });
            },
            reset() {
                this.file = null;
            }
        }
    }
</script>

<style scoped>

    .box {
        font-size: 1.25rem;
        background-color: #c8dadf;
        position: relative;
        padding: 100px 20px;
    }

    .box.has-advanced-upload {
        outline: 2px dashed #92b0b3;
        outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear;
    }

    .box.has-advanced-upload .box__dragndrop {
        display: inline;
    }

    .box.is-dragover {
        background-color: #e7e7e7;
        outline-offset: -15px;
    }

    .choose-file {
        transition: color 0.3s;
        cursor: pointer;
    }

    .choose-file:hover {
        color: #1eb9ff;
    }
</style>