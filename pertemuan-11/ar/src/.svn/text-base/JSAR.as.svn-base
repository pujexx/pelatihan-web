/*
 * JSAR.as - JSARToolKit
 * Copyright (c) 2009 Yusuke Kawasaki http://www.kawa.net/
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 *
 */

package {

    import flash.display.Bitmap;
    import flash.display.BitmapData;
    import flash.display.PixelSnapping;
    import flash.display.Sprite;
    import flash.events.Event;
    import flash.events.IOErrorEvent;
    import flash.events.SecurityErrorEvent;
    import flash.media.Camera;
    import flash.media.Video;
    import flash.media.Microphone;
    import flash.net.URLLoader;
    import flash.net.URLLoaderDataFormat;
    import flash.net.URLRequest;
    import flash.external.ExternalInterface;

    import org.libspark.flartoolkit.core.FLARCode;
    import org.libspark.flartoolkit.core.param.FLARParam;
    import org.libspark.flartoolkit.core.types.FLARIntPoint;
    import org.libspark.flartoolkit.core.raster.rgb.FLARRgbRaster_BitmapData;
    import org.libspark.flartoolkit.detector.FLARMultiMarkerDetector;
    import org.libspark.flartoolkit.detector.FLARMultiMarkerDetectorResult;

    [Event(name="init",type="flash.events.Event")]
    [Event(name="ioError",type="flash.events.IOErrorEvent")]
    [Event(name="securityError",type="flash.events.SecurityErrorEvent")]

    public class JSAR extends Sprite {

        private var _cameraLoader:URLLoader;
        private var _cameraFile:String;

        private var _codeLoader:URLLoader;
        private var _codeFiles:Array;       // String;
        private var _codeList:Array;        // FLARCode;
        private var _codeSizes:Array;       // int;

        private var _cameraReady:Boolean = false;
        private var _codeLoaded:int      = 0;

        private var _captureX:int        = 640;
        private var _captureY:int        = 480;
        private var _captureRate:int     = 30;
        private var _displayX:int        = 640;
        private var _displayY:int        = 480;
        private var _smoothing:Boolean   = false;
        private var _scaleX:Number       = 1.0;
        private var _scaleY:Number       = 1.0;
		private var _enableMic:Boolean    = false;

        private var _codeSize:int        = 80;
        private var _codePixel:int       = 16;
        private var _detectThreshold:int = 80;

        private var _param:FLARParam;
        private var _raster:FLARRgbRaster_BitmapData;
        private var _detector:FLARMultiMarkerDetector;

        private var _camera:Camera;
        private var _video:Video;
        private var _bmdata:BitmapData;
		private var _mic:Microphone;

        public function JSAR() {
            ExternalInterface.addCallback( 'initCamera', this.initCamera );
            ExternalInterface.addCallback( 'setMarker',  this.setMarker );
            ExternalInterface.addCallback( 'detectMarker', this.detectMarker );
        }

        protected function initCamera(opt:Object):void {
            if ( opt.cameraFile ) this._cameraFile = opt.cameraFile;
            if ( opt.captureX  ) this._captureX = opt.captureX;
            if ( opt.captureY ) this._captureY = opt.captureY;
            if ( opt.displayX  ) this._displayX = opt.displayX;
            if ( opt.displayY ) this._displayY = opt.displayY;
            if ( opt.smoothing ) this._smoothing = opt.smoothing;
            if ( opt.detectThreshold ) this._detectThreshold = opt.detectThreshold;
			if ( opt.enableMic ) this._enableMic = opt.enableMic;

            this._scaleX = 1.0 * this._displayX / this._captureX;
            this._scaleY = 1.0 * this._displayY / this._captureY;

            // setup webcam
            this._camera = Camera.getCamera();
            if (!this._camera) {
                throw new Error('No webcam!!!!');
            }
            this._camera.setMode(this._captureX, this._captureY, this._captureRate);

            // Video
            this._video = new Video(this._captureX, this._captureY);
            this._video.attachCamera(this._camera);

			// Mic
			if (this._enableMic) {
				this._mic = Microphone.getMicrophone();
				if (this._mic) {
					// this._mic.gain = 50.0;
					// this._mic.setUseEchoSuppression(false);
					this._mic.setLoopBack(true);
				}
			}
				
            // Bitmap
            this._bmdata = new BitmapData(this._captureX, this._captureY, false, 0);
            this._raster = new FLARRgbRaster_BitmapData(this._bmdata);
            var bitmap:Bitmap = new Bitmap(this._bmdata, PixelSnapping.AUTO, this._smoothing);
            bitmap.scaleX = this._scaleX;
            bitmap.scaleY = this._scaleY;
            this.addChild(bitmap);

			// load camera param
            this._param = new FLARParam();
			this._cameraLoader = new URLLoader();
            this._cameraLoader.addEventListener(IOErrorEvent.IO_ERROR, this.dispatchEvent);
            this._cameraLoader.addEventListener(SecurityErrorEvent.SECURITY_ERROR, this.dispatchEvent);
            this._cameraLoader.addEventListener(Event.COMPLETE, this._onLoadedParam);
            this._cameraLoader.dataFormat = URLLoaderDataFormat.BINARY;
            this._cameraLoader.load(new URLRequest(this._cameraFile));
        }

        private function _onLoadedParam(e:Event):void {
            // this._info( '_onLoadedParam e='+e );
            this._cameraLoader.removeEventListener(Event.COMPLETE, this._onLoadedParam);

			// set camera parameters
            this._param.loadARParam(this._cameraLoader.data);
			
			// set capture size again
            this._param.changeScreenSize(this._captureX, this._captureY);

            // loading done
            this._cameraLoader.removeEventListener(IOErrorEvent.IO_ERROR, this.dispatchEvent);
            this._cameraLoader.removeEventListener(SecurityErrorEvent.SECURITY_ERROR, this.dispatchEvent);
            this._cameraLoader = null;
            this._cameraReady  = true;
        }

        protected function setMarker(list:Array):void {
            if ( list && list.length ) this._codeFiles = list;
            this._codeList   = new Array();
            this._codeSizes  = new Array();
            this._codeLoaded = 0;
            this._codeLoader = new URLLoader();
            this._codeLoader.addEventListener(IOErrorEvent.IO_ERROR, this.dispatchEvent);
            this._codeLoader.addEventListener(SecurityErrorEvent.SECURITY_ERROR, this.dispatchEvent);
            this._loadNextCode();
        }

        private function _loadNextCode():void {
            // this._info( '_loadNextCode _codeLoaded=' + this._codeLoaded + ' / ' + this._codeFiles.length );
            if ( this._codeLoaded >= this._codeFiles.length ) {
                this._onCompleteLoadCode();
            } else {
                // load pattern file
                var codeFile:String = this._codeFiles[this._codeLoaded];
                // this._info( '_loadNextCode codeFile=' + codeFile );

                this._codeLoader.addEventListener(Event.COMPLETE, this._onLoadedCode);
                this._codeLoader.dataFormat = URLLoaderDataFormat.TEXT;
                this._codeLoader.load(new URLRequest(codeFile));
            }
        }

        private function _onLoadedCode(e:Event):void {
            // this._info( '_onLoadedCode _codeLoaded=' + this._codeLoaded );
            this._codeLoader.removeEventListener(Event.COMPLETE, this._onLoadedCode);

            var acode:FLARCode = new FLARCode(this._codePixel, this._codePixel);
            acode.loadARPatt(this._codeLoader.data);

            this._codeList[this._codeLoaded] = acode;
            this._codeSizes[this._codeLoaded] = this._codeSize;
            this._codeLoaded ++;
            this._loadNextCode();
        }

        private function _onCompleteLoadCode():void {
            // this._info( '_onCompleteLoadCode this._codeLoaded=' + this._codeLoaded );

            // loading done
            this._codeLoader.removeEventListener(IOErrorEvent.IO_ERROR, this.dispatchEvent);
            this._codeLoader.removeEventListener(SecurityErrorEvent.SECURITY_ERROR, this.dispatchEvent);
            this._codeLoader = null;

            // setup ARToolkit
            this._detector = new FLARMultiMarkerDetector(this._param, this._codeList, this._codeSizes, this._codeList.length)
        }

        protected function detectMarker( callback:String ):void {
            var arg:Object = { detected: false };
            this._getDetectResult( arg );
            if ( arg.error ) {
                this._info( 'detection failed: ' + arg.error);
            }
            this._call_javascript( callback, arg );
        }

        private function _getDetectResult( arg:Object ):void {
            // camera is denied by user
            if ( this._camera.muted ) {
                arg.error = 'camera_denied';
                return;
            }

			// param file not loaded
            if ( ! this._cameraReady ) {
                arg.error = 'camera_not_ready';
                return;
            }

			// fetch mic activity
			if (this._mic) {
				var vol:Number = this._mic.activityLevel
//				this._info( 'vol='+vol );
				if ( vol >= 0 ) arg.mic = vol;
			}

            // draw bitmap video frame
            this._bmdata.draw(this._video);

            // check code loaded
            if ( ! this._detector ) {
                arg.error = 'detector_not_loaded';
                return;
            }

            var num:int;
            try {
                num = this._detector.detectMarkerLite(this._raster, this._detectThreshold);
            } catch (e:Error) {
                arg.error = e;
                return;
            }
            if ( num == 0 ) return;

            var markers:Array = [];
            for (var i:int = 0; i < num; i++) {
                if ( this._detector.getConfidence(i) < 0.5) continue;
                var res:FLARMultiMarkerDetectorResult = this._detector.getResult(i);
                var vertex:Array = new Array(4);
                for ( var j:int = 0; j < 4; j++ ) {
                    var ipoint:FLARIntPoint = res.square.imvertex[j];
                    var apoint:Object = {
                        x: Math.floor(ipoint.x * this._scaleX),
                        y: Math.floor(ipoint.y * this._scaleY)
                    };
                    // this._info( 'x=' + apoint.x + ' y=' + apoint.y );
                    vertex[j] = apoint;
                }
                var hash:Object = {
                    codeid:     res.codeId,
                    direction:  res.direction,
                    confidence: res.confidence.toFixed(6),
                    vertex:     vertex
                };
                markers.push( hash );
            }

            if ( markers.length > 0 ) {
                arg.detected = true;
                arg.markers  = markers;
            }
        }

		// message to javascript console
		private function _info( mess:String ):void {
            ExternalInterface.call( "(function(m){if(window.console)console.log(m);})",mess );
        }

		// javascript callback
        private function _call_javascript( ... args ):void {
			try {
	            ExternalInterface.call.apply( null, args );
			} catch (e:Error) {
				this._err( 'callback failed: ' + e.message );
			}
        }
    }
}
