import { Component, OnInit, ViewChild, ElementRef, Renderer2, EventEmitter, AfterViewInit } from '@angular/core';
import { Subject, Subscription } from 'rxjs';
import { QRCode } from 'src/lib/qr-decoder/qrcode';
import { DBLettersService, LetterStatus } from 'src/app/services/dbletters.service';
import { StorageService } from 'src/app/services/storage.service';

@Component({
  selector: 'app-qr-reader',
  templateUrl: './qr-reader.component.html',
  styleUrls: ['./qr-reader.component.sass']
})
export class QrReaderComponent implements OnInit, AfterViewInit {
  scannedIds: number[] = [];
  devices = [
    { value: 'none', label: 'Выберите камеру' }
  ];
  isCameraHiiden = true;

  /*
  for test on mobile:

  start serve with command
  ng s --host 0.0.0.0 --disable-host-check

  and another command window:
  ssh -R 80:0.0.0.0:4200 ssh.localhost.run

  and for backend on MAMP ssh -R 80:0.0.0.0:8888 ssh.localhost.run
  and Replace url in environment (mb --prod if deploy)
  */


  @ViewChild('result', { static: true }) resultElement: ElementRef;
  @ViewChild('videoWrapper', { static: true }) videoWrapper: ElementRef;
  @ViewChild('selecting', { static: true }) selecting: ElementRef;
  @ViewChild('qrCanvas', { static: true }) qrCanvas: ElementRef;

  canvasWidth = 640;
  canvasHeight = 480;
  offsetVideo = '-480px';
  debug = false;
  stopAfterScan = true;
  updateTime = 100;

  capturedQr: EventEmitter<string> = new EventEmitter();
  foundCameras: EventEmitter<MediaDeviceInfo[]> = new EventEmitter();

  chooseCamera: Subject<MediaDeviceInfo> = new Subject();

  public gCtx: CanvasRenderingContext2D;
  public qrCode: QRCode;
  public stream: MediaStream;
  public captureTimeout: any;
  public canvasHidden = true;
  get isCanvasSupported(): boolean {
    const canvas = this.renderer.createElement('canvas');
    return !!(canvas.getContext && canvas.getContext('2d'));
  }

  constructor(
    private renderer: Renderer2,
    private elementRef: ElementRef,
    private apiService: DBLettersService,
    private storageService: StorageService
  ) { }

  clearAll() {
    this.storageService.clear();
  }

  ngOnInit() {
    this.storageService.getAll().subscribe(r => {
      this.scannedIds = r.map(rr => rr.ider);
      console.log( this.scannedIds);
    });

    (this.videoWrapper.nativeElement as HTMLVideoElement).addEventListener('playing', (e) => {
      const r = (this.videoWrapper.nativeElement as HTMLVideoElement).getClientRects();
      this.canvasHeight = r.item(0).height;
      this.offsetVideo = `-${this.canvasHeight}px`;
      this.canvasWidth = r.item(0).width;
    });

    this.getStream().then(this.getDevices).then(this.gotDevices);
  }

  ngAfterViewInit() {
    if (this.isCanvasSupported) {
      this.gCtx = this.qrCanvas.nativeElement.getContext('2d');
      this.gCtx.clearRect(0, 0, this.canvasWidth, this.canvasHeight);
      this.qrCode = new QRCode();
      this.qrCode.myCallback = (decoded: string) => this.QrDecodeCallback(decoded);
    }
  }

  stopScanning() {
    if (this.captureTimeout) {
      clearTimeout(this.captureTimeout);
      this.captureTimeout = 0;
    }
    this.canvasHidden = false;

    const stream = this.stream && this.stream.getTracks().length && this.stream;
    if (stream) {
      stream.getTracks().forEach(track => track.enabled && track.stop());
      this.stream = null;
    }
  }

  sendAll() {
    this.apiService.updateStatusLetter(this.scannedIds, this.storageService.status.getValue())
      .subscribe(res => {
        alert('Статус успешно сменен');
      }, error => {
        for (const key in error) {
          if (error.hasOwnProperty(key)) {
            alert(key + ' - ' + error[key]);
          }
        }
      });
  }

  public QrDecodeCallback(decoded: string) {
    decoded = decoded.substr(0, decoded.length - 4);
    const id = +decoded;

    if (!this.scannedIds.find(ider => ider === id) && typeof id === 'number' && !Number.isNaN(id)) {
      this.storageService.setId(id).subscribe(r => {
        // console.log(r);
      });

      // alert(`Код успешно просканирован - ${id}`);
      this.scannedIds.push(id);
      this.captureTimeout = setTimeout(() => this.captureToCanvas(), this.updateTime);

    } else {
      if (this.scannedIds.find(ider => ider === id)) {
        alert('Этот QR-код уже был просканнирован');
      }

      this.captureTimeout = setTimeout(() => this.captureToCanvas(), this.updateTime);
    }
  }

  private captureToCanvas() {
    try {
      this.gCtx.drawImage(this.videoWrapper.nativeElement, 0, 0, this.canvasWidth, this.canvasHeight);
      this.qrCode.decode(this.qrCanvas.nativeElement);
    } catch (e) {
      if (!this.stream) {
        return;
      }
      this.captureTimeout = setTimeout(() => this.captureToCanvas(), this.updateTime);
    }
  }

  private setStream(stream: any) {
      this.canvasHidden = true;
      this.gCtx.clearRect(0, 0, this.canvasWidth, this.canvasHeight);
      this.stream = stream;
      this.videoWrapper.nativeElement.srcObject = stream;
      this.captureTimeout = setTimeout(() => this.captureToCanvas(), this.updateTime);
  }

  getDevices() {
    return navigator.mediaDevices.enumerateDevices();
  }

  gotDevices = (deviceInfos) => {
    (window as any).deviceInfos = deviceInfos; // may not need, realy, i have tested it
    for (const deviceInfo of deviceInfos) {
      if (deviceInfo.kind === 'videoinput') {
        this.devices.push({
          value: deviceInfo.deviceId,
          label: deviceInfo.label
        });
      }
    }
  }

  getStream = (data?) => {
    if (data && this.isCameraHiiden) {
      this.isCameraHiiden = false;
    }

    if ((window as any).stream) {
      (window as any).stream.getTracks().forEach(track => {
        track.stop();
      });
    }

    const videoSource = this.selecting.nativeElement.value;

    if (videoSource === 'none') {
      return;
    }

    const constraints = {
      audio: false,
      video: {deviceId: videoSource ? {exact: videoSource} : undefined}
    };
    return navigator.mediaDevices.getUserMedia(constraints).
      then(this.gotStream).catch(this.handleError);
  }

  gotStream = (stream: MediaStream) => {
    (window as any).stream = stream;
    this.selecting.nativeElement.selectedIndex = [...this.selecting.nativeElement.options].
      findIndex(option => option.text === stream.getVideoTracks()[0].label);

    this.setStream(stream);
  }

  handleError(error) {
    console.error('Error: ', error);
  }

  appendMessageToResult(text: string) {
    const textWrapper = this.renderer.createElement('span');
    textWrapper.innerHTML = text;
    this.renderer.appendChild(this.resultElement.nativeElement, textWrapper);
  }
}
