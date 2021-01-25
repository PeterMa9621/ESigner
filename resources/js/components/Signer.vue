<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="row fixed-top tool-bar">
                        <div class="col-xl-3 col-md-1"></div>
                        <div class="col-xl-6 col-md-10 col-sm-12">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <button class="scale-btn mr-2" @click="() => showPdf({scaleNumber: -0.1})">&#8722;</button>
                                    <button class="scale-btn" @click="() => showPdf({scaleNumber: 0.1})">&#43;</button>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Scaling  {{ Math.round(currentScale * 100) + '%' }}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="#" @click="showPdf({setScaleTo: 1})">100%</a>
                                        <a class="dropdown-item" href="#" @click="showPdf({setScaleTo: 1.5})">150%</a>
                                        <a class="dropdown-item" href="#" @click="showPdf({setScaleTo: 2})">200%</a>
                                    </div>
                                </div>
                                <div class="lg-operation">
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#signModal" @click="initCanvas">
                                        {{ isSigned?'&#128221; Edit':'&#9997; Sign' }}
                                    </button>
                                    <button v-if="isSigned" type="button" class="btn btn-success" @click="downloadPdf">
                                        &#128190; Download
                                    </button>
                                </div>
                                <div class="sm-operation">
                                    <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Operation
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#signModal" @click="initCanvas">{{ isSigned?'&#128221; Edit':'&#9997; Sign' }}</a>
                                        <a v-if="isSigned" class="dropdown-item" href="#" @click="downloadPdf">&#128190; Download</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-1"></div>
                    </div>
                    <div class="row pt-5">
                        <div class="col text-center">
                            {{ title }}
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-xl-3 col-md-1"></div>
                        <div class="col-xl-6 col-md-10 col-sm-12 align-self-center">
                            <div class="row text-center">
                                <div class="col-4"></div>
                                <div class="col-4 align-self-center">
                                    <span class="pagination-box">{{ currentPage }} / {{ numPages }}</span>
                                </div>
                                <div class="col-4">
                                    <button class="pagination-btn mr-2" :disabled="currentPage<=1" @click="() => showPdf({page: currentPage-1})">&#8249;</button>
                                    <button class="pagination-btn" :disabled="currentPage>=numPages" @click="() => showPdf({page: currentPage+1})">&#8250;</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-1"></div>
                    </div>
                    <div id="pdf-container" class="pdf-container text-center mt-1">
                        <canvas id="pdf-canvas"></canvas>
                    </div>

                    <div class="loading" v-if="isLoading">
                        <div class="sk-chase">
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade w-100" id="signModal" tabindex="-1" role="dialog" aria-labelledby="signModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered w-100" role="document">
                <div class="modal-content w-100">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Please draw your signature here</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body w-100">
                        <div class="text-center w-100">
                            <canvas id="signature-canvas"></canvas>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="clear" type="button" class="btn btn-primary" @click="clear">Clear</button>
                        <button id="finish" type="button" class="btn btn-success" @click="finish" data-dismiss="modal">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import * as pdfjsLib from 'pdfjs-dist';
    import PDFJSWorker from '!!file-loader!pdfjs-dist/build/pdf.worker.min.js';
    import SignaturePad from "signature_pad";
    import SignService from "../services/SignService";

    const SIGNATURE_WIDTH = 50;
    const SIGNATURE_HEIGHT = 25;
    const BASE64_MARKER = ';base64,';
    function convertDataURIToBinary(dataURI) {
        //const base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
        //const base64 = dataURI.substring(base64Index);
        const raw = window.atob(dataURI);
        const rawLength = raw.length;
        const array = new Uint8Array(new ArrayBuffer(rawLength));

        for(let i = 0; i < rawLength; i++) {
            array[i] = raw.charCodeAt(i);
        }
        return array;
    }

    function getQueryStringValue (key) {
        return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
    }

    export default {
        name: "Signer",
        props: ['documentId', 'title', 'src', 'hasSigned', 'signaturePosition'],
        data() {
            return {
                signaturePad: null,
                isLoading: false,
                currentScale: 1,
                doc: null,
                pdfBase64: this.src,
                hasInitPdfContainer: false,
                isSigned: this.hasSigned,
                currentPage: 1,
                numPages: 1,
            }
        },
        mounted() {
            pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJSWorker;
            this.initPdf(this.pdfBase64);

            let canvas = document.getElementById("signature-canvas");
            this.signaturePad = new SignaturePad(canvas, {
                dotSize: 3,
                minWidth: 3,
                maxWidth: 3
            });
            canvas.width = canvas.parentElement.clientWidth;
            canvas.height = 200;
        },
        methods: {
            initPdf(base64) {
                pdfjsLib.getDocument(convertDataURIToBinary(base64)).promise.then(doc => {
                    this.numPages = doc.numPages;
                    this.doc = doc;
                    this.showPdf({});
                });
            },
            initCanvas() {
                let canvas = document.getElementById("signature-canvas");
                let interval = setInterval(() => {
                    let parentWidth = canvas.parentElement.clientWidth;
                    if(parentWidth > 0 && canvas.width!==parentWidth) {
                        canvas.width = canvas.parentElement.clientWidth;
                        canvas.height = 200;
                        clearInterval(interval);
                    }
                }, 100);
            },
            downloadPdf() {
                const linkSource = 'data:application/pdf;base64,' + this.pdfBase64;
                const downloadLink = document.createElement("a");
                downloadLink.href = linkSource;
                downloadLink.download = 'your_signed_form.pdf';
                downloadLink.click();
            },
            drawSignaturePositionRect(context) {
                if(Object.keys(this.signaturePosition).length === 0) {
                    return;
                }

                if(this.currentPage !== this.signaturePosition.page)
                    return;

                context.fillStyle = 'rgba(0,182,162,0.76)';
                context.fillRect(this.signaturePosition.x * this.currentScale, this.signaturePosition.y * this.currentScale, SIGNATURE_WIDTH * this.currentScale, SIGNATURE_HEIGHT * this.currentScale);
                let fontSize = 10.5 * this.currentScale;
                context.font = fontSize + "px Comic Sans MS";
                context.fillStyle = "#000000";
                let textCenterX = this.signaturePosition.x * this.currentScale;
                let textCenterY = (this.signaturePosition.y * this.currentScale + fontSize/2 + (this.signaturePosition.y * this.currentScale + SIGNATURE_HEIGHT * this.currentScale))/2;
                context.fillText("Sign Here", textCenterX, textCenterY);
            },
            showPdf({scaleNumber=0, setScaleTo=null, page=1}) {
                if(this.currentPage > this.numPages)
                    return;
                if(this.currentScale + scaleNumber < 1)
                    return;
                if(!setScaleTo)
                    this.currentScale += scaleNumber;
                else
                    this.currentScale = setScaleTo;
                this.currentScale = +this.currentScale.toFixed(2);
                this.currentPage = page;
                this.doc.getPage(this.currentPage).then(page => {
                    let canvas = document.getElementById('pdf-canvas');
                    let context = canvas.getContext('2d');
                    context.rotate(90* Math.PI/180);
                    let viewport = page.getViewport({scale: this.currentScale, rotation: 0, dontFlip: false});
                    //var neededViewport = viewport.clone({scale: 1, rotation: 0, dontFlip: false});
                    let pdfContainer = document.getElementById('pdf-container');
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    if(!this.hasInitPdfContainer) {
                        let width = viewport.width + 10;
                        let height = viewport.height + 10;
                        if(width > window.innerWidth)
                            width = width.innerWidth;
                        if(height > window.innerHeight)
                            height = height.innerHeight;

                        pdfContainer.style.width = width;
                        pdfContainer.style.height = height;
                        this.hasInitPdfContainer = true;
                    }

                    page.render({
                        canvasContext: context,
                        viewport: viewport
                    }).promise.then(() => {
                        if(!this.isSigned)
                            this.drawSignaturePositionRect(context);
                    });
                })
            },
            clear() {
                this.signaturePad.clear();
            },
            finish() {
                this.isLoading = true;
                let data = this.signaturePad.toDataURL();
                SignService.sign(this.documentId, { signature: data }).then(response => {
                    this.pdfBase64 = response['pdf'];
                    this.initPdf(response['pdf']);
                    this.isSigned = true;
                    this.$swal('Succeed!', 'You signed the form successfully', 'success');
                }).catch(reason => {
                    console.log(reason);
                    this.$swal('Failed!', 'Error occurs, you failed to sign the form', 'error');
                }).finally(() => {
                    this.isLoading = false;
                });
                /*
                $.ajax({
                    url: '/api/csio_form/sign',
                    data: JSON.stringify({
                        signature: data,
                        token: getQueryStringValue('token')
                    }),
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    success: (response) => {
                        this.$swal('Succeed!', 'You signed the form successfully', 'success');
                        this.initPdf(response['pdf']);
                        this.isSigned = true;
                    },
                    error: (response) => {
                        console.log(response);
                        this.$swal('Failed!', 'Error occurs, you failed to sign the form', 'error');
                    }
                }).always(() => {
                    this.isLoading = false;
                });

                 */
            }
        }
    }
</script>

<style scoped>
    .sm-operation {
        display: none;
    }
    @media screen and (max-width: 1024px) {
        .lg-operation {
            display: none !important;
        }
        .sm-operation {
            display: block !important;
        }
    }

    .tool-bar {
        background-color: #343a40;
        padding-top: 5px;
        padding-bottom: 5px;
        margin-top: 3.5rem;
    }

    .scale-btn {
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

    .scale-btn:hover {
        color: #fff;
        background-color: #85aebf;
    }

    .pdf-container {
        overflow: auto;
    }

    canvas {
        border: 1px solid;
    }

    .sk-chase {
        top: 50%;
        left: 50%;
        width: 40px;
        height: 40px;
        position: fixed;
        z-index: 9;
        animation: sk-chase 2.5s infinite linear both;
    }

    .sk-chase-dot {
        width: 100%;
        height: 100%;
        position: absolute;
        left: 0;
        top: 0;
        animation: sk-chase-dot 2.0s infinite ease-in-out both;
    }

    .sk-chase-dot:before {
        content: '';
        display: block;
        width: 25%;
        height: 25%;
        background-color: #fcfaff;
        border-radius: 100%;
        animation: sk-chase-dot-before 2.0s infinite ease-in-out both;
    }

    .sk-chase-dot:nth-child(1) { animation-delay: -1.1s; }
    .sk-chase-dot:nth-child(2) { animation-delay: -1.0s; }
    .sk-chase-dot:nth-child(3) { animation-delay: -0.9s; }
    .sk-chase-dot:nth-child(4) { animation-delay: -0.8s; }
    .sk-chase-dot:nth-child(5) { animation-delay: -0.7s; }
    .sk-chase-dot:nth-child(6) { animation-delay: -0.6s; }
    .sk-chase-dot:nth-child(1):before { animation-delay: -1.1s; }
    .sk-chase-dot:nth-child(2):before { animation-delay: -1.0s; }
    .sk-chase-dot:nth-child(3):before { animation-delay: -0.9s; }
    .sk-chase-dot:nth-child(4):before { animation-delay: -0.8s; }
    .sk-chase-dot:nth-child(5):before { animation-delay: -0.7s; }
    .sk-chase-dot:nth-child(6):before { animation-delay: -0.6s; }

    @keyframes sk-chase {
        100% { transform: rotate(360deg); }
    }

    @keyframes sk-chase-dot {
        80%, 100% { transform: rotate(360deg); }
    }

    @keyframes sk-chase-dot-before {
        50% {
            transform: scale(0.4);
        } 100%, 0% {
              transform: scale(1.0);
          }
    }

    .loading {
        left: 0;
        top: 0;
        position: fixed;
        z-index: 9999;
        height: 100%;
        width: 100%;
        background-color: rgba(128, 128, 128, 0.5);
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

    .pagination-box {
        background-color: rgba(29,29,30,0.5);
        padding: 2px 10px;
        color: #f1f5ef;
        border-radius: 5px;
    }

    .fixed-top {
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1029;
    }
</style>