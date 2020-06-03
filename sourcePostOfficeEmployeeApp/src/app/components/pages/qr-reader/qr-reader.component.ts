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
  // selectValue;

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


  // @ViewChild(QrScannerComponent, { static: true }) qrScannerComponent: QrScannerComponent ;
  @ViewChild('result', { static: true }) resultElement: ElementRef;
  @ViewChild('videoWrapper', { static: true }) videoWrapper: ElementRef;
  @ViewChild('selecting', { static: true }) selecting: ElementRef;

  // start
  canvasWidth = 640;
  canvasHeight = 480;
  offsetVideo = '-480px';
  debug = false;
  stopAfterScan = true;
  updateTime = 100;

  capturedQr: EventEmitter<string> = new EventEmitter();
  foundCameras: EventEmitter<MediaDeviceInfo[]> = new EventEmitter();

  @ViewChild('qrCanvas', { static: true }) qrCanvas: ElementRef;

  chooseCamera: Subject<MediaDeviceInfo> = new Subject();

  private chooseCamera$: Subscription;

  public gCtx: CanvasRenderingContext2D;
  // public videoElement: HTMLVideoElement;
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
    // this.storageService.clear();
    this.storageService.getAll().subscribe(r => {
      this.scannedIds = r.map(rr => rr.ider);
      console.log( this.scannedIds);

    });
    // const r = this.videoWrapper.nativeElement;

    (this.videoWrapper.nativeElement as HTMLVideoElement).addEventListener('playing', (e) => {
      const r = (this.videoWrapper.nativeElement as HTMLVideoElement).getClientRects();
      this.canvasHeight = r.item(0).height;
      this.offsetVideo = `-${this.canvasHeight}px`;
      this.canvasWidth = r.item(0).width;
      // console.log(this.videoWrapper.nativeElement);
    });
    // console.log(r);

    // this.canvasWidth = r;

    this.getStream().then(this.getDevices).then(this.gotDevices);
  }

  // ngOnDestroy() {
  //   this.chooseCamera$.unsubscribe();
  //   this.stopScanning();
  // }

  ngAfterViewInit() {
    // if (this.debug) console.log('[QrScanner] ViewInit, isSupported: ', this.isCanvasSupported);
    if (this.isCanvasSupported) {
      this.gCtx = this.qrCanvas.nativeElement.getContext('2d');
      this.gCtx.clearRect(0, 0, this.canvasWidth, this.canvasHeight);
      this.qrCode = new QRCode();
      // if (this.debug) this.qrCode.debug = true;
      this.qrCode.myCallback = (decoded: string) => this.QrDecodeCallback(decoded);
    }
    // this.chooseCamera$ = this.chooseCamera.subscribe((camera: MediaDeviceInfo) => this.useDevice(camera));
    // this.getMediaDevices().then(devices => this.foundCameras.next(devices));
  }

  // startScanning(device: MediaDeviceInfo) {
  //   this.useDevice(device);
  // }

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

  // getMediaDevices(): Promise<MediaDeviceInfo[]> {
  //     if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) return Promise.resolve([]);
  //     return navigator.mediaDevices.enumerateDevices()
  //       .then((devices: MediaDeviceInfo[]) => devices)
  //       .catch((error: any): any[] => {
  //         if (this.debug) console.warn('Error', error);
  //         return [];
  //       });
  // }

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
    // if (this.stopAfterScan) {
    //   this.stopScanning();
    //   this.capturedQr.next(decoded);
    // } else {
    // }
    decoded = decoded.substr(0, decoded.length - 4);
    // decoded = decoded.
    const id = +decoded;

    if (!this.scannedIds.find(ider => ider === id) && typeof id === 'number' && !Number.isNaN(id)) {
      // this.stopScanning();
      // this.capturedQr.next(decoded);
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
      // this.capturedQr.next(decoded);
      // this.appendMessageToResult(decoded); // stop scanning and link to letter information
      this.captureTimeout = setTimeout(() => this.captureToCanvas(), this.updateTime);
    }
  }

  private captureToCanvas() {
    try {
      this.gCtx.drawImage(this.videoWrapper.nativeElement, 0, 0, this.canvasWidth, this.canvasHeight);
      this.qrCode.decode(this.qrCanvas.nativeElement);
    } catch (e) {
      // if (this.debug) console.log('[QrScanner] Thrown', e);
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

  // private useDevice(_device: MediaDeviceInfo) {
  //   const _navigator: any = navigator;

  //   if (this.captureTimeout) {
  //       this.stopScanning();
  //   }

  //   if (!this.videoElement) {
  //       this.videoElement = this.renderer.createElement('video');
  //       this.videoElement.setAttribute('autoplay', 'true');
  //       this.videoElement.setAttribute('muted', 'true');
  //       this.renderer.appendChild(this.videoWrapper.nativeElement, this.videoElement);
  //   }
  //   const self = this;

  //   let constraints: MediaStreamConstraints;
  //   if (_device) {
  //     constraints = {audio: false, video: {deviceId: _device.deviceId}};
  //   } else {

  //     constraints = {audio: false, video: true};
  //   }
  //   _navigator.mediaDevices.getUserMedia(constraints).then(function (stream) {
  //       self.setStream(stream);
  //   }).catch(function (err) {
  //       return self.debug && console.warn('Error', err);
  //   });
  // }

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
      // hide videoWrapper
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
    (window as any).stream = stream; // may not need
    this.selecting.nativeElement.selectedIndex = [...this.selecting.nativeElement.options].
      findIndex(option => option.text === stream.getVideoTracks()[0].label); // may not need

    this.setStream(stream);
    // this.videoWrapper.nativeElement.srcObject = stream;
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
