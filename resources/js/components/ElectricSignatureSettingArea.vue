<template>
    <div>
        <div class="text-center">
            <h3 class="modal-title">Setup signature position</h3>
        </div>

        <div class="row">
            <div class="col text-center mt-3">
                <div>
                    <div class="container-fluid">
                        <div class="alert alert-primary mb-0" role="alert">
                            Please click one area on the pdf to select the signature position
                        </div>
                    </div>
                    <div class="container-fluid mt-2">
                        <div class="row">
                            <div class="col-4"></div>
                            <div class="col-4 align-self-center">
                                <span class="pagination-box">{{ currentPage }} / {{ numPages }}</span>
                            </div>
                            <div class="col-4">
                                <button class="pagination-btn mr-2" :disabled="currentPage<=1" @click="() => renderPdf(currentPage-1)">&#8249;</button>
                                <button class="pagination-btn" :disabled="currentPage>=numPages" @click="() => renderPdf(currentPage+1)">&#8250;</button>
                            </div>
                        </div>
                    </div>
                    <div id="pdf-container" class="pdf-container text-center mt-1">
                        <canvas class="pdf-canvas" ref="pdf-canvas" id="pdf-canvas" @mousedown="getCursorPosition"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid mt-2">
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-12 col-md-4 text-right">
                    <button type="button" class="btn btn-secondary" @click="reupload">Re-upload</button>
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#signModal">Save</button>
                </div>
            </div>
        </div>

        <div class="modal fade w-100" id="signModal" tabindex="-1" role="dialog" aria-labelledby="signModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered w-100" role="document">
                <div class="modal-content w-100">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">One more step</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body w-100">
                        <div>
                            <label for="documentTitle">Enter the document title (optional)</label>
                            <input id="documentTitle" class="form-control" v-model="title">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button id="finish" type="button" class="btn btn-success" @click="save" data-dismiss="modal">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import * as pdfjsLib from 'pdfjs-dist';
    import PDFJSWorker from '!!file-loader!pdfjs-dist/build/pdf.worker.min.js';
    import SignaturePositionService from "../services/SignaturePositionService";

    const SIGNATURE_WIDTH = 50;
    const SIGNATURE_HEIGHT = 25;

    const BASE64_MARKER = ';base64,';
    function convertDataURIToBinary(dataURI) {
        let base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
        let base64 = dataURI.substring(base64Index);
        let raw = window.atob(base64);
        let rawLength = raw.length;
        let array = new Uint8Array(new ArrayBuffer(rawLength));

        for(let i = 0; i < rawLength; i++) {
            array[i] = raw.charCodeAt(i);
        }
        return array;
    }

    export default {
        name: "ElectricSignatureSettingArea",
        props: ['pdfData'],
        data() {
            return {
                currentScale: 1,
                doc: null,
                signaturePosition: {},
                signaturePositionRect: null,
                currentPage: 1,
                numPages: 1,
                error: '',
                pdfWidth: 0,
                pdfHeight: 0,
                title: ''
            }
        },
        mounted() {
            pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJSWorker;
            this.initPdf();
        },
        methods: {
            initPdf() {
                pdfjsLib.getDocument(convertDataURIToBinary(this.pdfData)).promise.then(doc => {
                    this.numPages = doc.numPages;
                    this.doc = doc;
                    this.renderPdf();
                });
            },
            renderPdf(page=1) {
                if(this.currentPage !== page)
                    this.signaturePositionRect = null;
                this.currentPage = page;
                this.doc.getPage(page).then(page => {
                    let canvas = this.$refs['pdf-canvas'];
                    let context = canvas.getContext('2d');
                    let viewport = page.getViewport({scale: this.currentScale, rotation: 0, dontFlip: false});

                    this.pdfWidth = viewport.width;
                    this.pdfHeight = viewport.height;
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    page.render({
                        canvasContext: context,
                        viewport: viewport
                    }).promise.then(() => {
                        this.drawSignaturePositionRect(context);
                    });
                });
            },
            drawSignaturePositionRect(context) {
                if(Object.keys(this.signaturePosition).length === 0 || this.signaturePosition['page'] !== this.currentPage) {
                    this.signaturePositionRect = null;
                    return;
                }

                this.signaturePositionRect = context.getImageData(this.signaturePosition.x, this.signaturePosition.y, SIGNATURE_WIDTH + 1, SIGNATURE_HEIGHT);
                context.fillStyle = 'rgba(0,182,162,0.76)';
                context.fillRect(this.signaturePosition.x, this.signaturePosition.y, SIGNATURE_WIDTH, SIGNATURE_HEIGHT);
            },
            save() {
                if(Object.keys(this.signaturePosition).length === 0) {
                    this.$swal('Failed!', 'You need to select an area to put your signature on', 'error');
                    return;
                }

                SignaturePositionService.createSignaturePosition(this.signaturePosition).then(signaturePosition => {
                    this.$emit('onSucceed', {
                        signaturePosition: signaturePosition.data,
                        pdfWidth: this.pdfWidth,
                        pdfHeight: this.pdfHeight,
                        numPages: this.numPages,
                        title: this.title
                    });
                }).catch(reason => {
                    console.log(reason);
                    this.$swal('Failed!', 'Error occurs when you choose signature position', 'error');
                });
            },
            getCursorPosition(event) {
                let canvas = document.getElementById('pdf-canvas');
                let context = canvas.getContext('2d');
                if(this.signaturePositionRect !== null)
                    context.putImageData(this.signaturePositionRect, this.signaturePosition.x, this.signaturePosition.y);
                context.restore();
                const rect = canvas.getBoundingClientRect();
                const x = event.clientX - rect.left;
                const y = event.clientY - rect.top;

                this.signaturePosition.x = x;
                this.signaturePosition.y = y;
                this.signaturePosition.page = this.currentPage;

                this.drawSignaturePositionRect(context);

                context.save();
                context.restore();
            },
            reupload() {
                this.$emit('reupload');
            }
        }
    }
</script>

<style scoped>
    .pagination-box {
        background-color: rgba(29,29,30,0.5);
        padding: 2px 10px;
        color: #f1f5ef;
        border-radius: 5px;
    }

    .pagination-btn {
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: inline-block;
        border: 1px solid #dee6ed;
        color: black;
        background-color: #27bfaa;
        text-decoration: none;
        transition: 0.5s;
    }

    .pagination-btn:hover {
        color: #fff;
        background-color: #85aebf;
        cursor: pointer;
    }

    .pagination-btn:disabled {
        color: #dbdbdb;
        background-color: #7198a9;
        cursor: default;
    }

    .pdf-canvas {
        cursor: cell;
    }

    .pdf-container {
        overflow: auto;
    }

    canvas {
        border: 1px solid;
    }
</style>
