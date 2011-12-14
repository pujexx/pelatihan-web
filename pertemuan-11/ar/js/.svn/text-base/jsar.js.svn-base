/* 
 * jsar.js - JSARToolKit
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

//  constructor

window.JSAR = function ( jsarId ) {
    if ( jsarId ) this.jsarId = jsarId;
    JSAR.Instances ++;
    this.instanceId = JSAR.Instances;
    return this;
};

JSAR.prototype.jsarId     = 'jsar_canvas';
JSAR.prototype.jsarSWF    = '../swf/jsar.swf';
JSAR.prototype.installSWF = '../swf/expressInstall.swf';
JSAR.prototype.cameraFile = '../swf/camera_para.dat'
JSAR.prototype.markerFiles;
JSAR.prototype.noCache    = true;
JSAR.prototype.enableMic  = false;

JSAR.prototype.captureX  = 320;
JSAR.prototype.captureY  = 240;
JSAR.prototype.displayX  = 640;
JSAR.prototype.displayY  = 480;
JSAR.prototype.asyncWait = 1;

JSAR.prototype.flash_wmode      = 'transparent';
JSAR.prototype.flash_quality    = 'low';
JSAR.prototype.flash_scale      = 'noScale';
JSAR.prototype.flash_salign     = 'lt';
JSAR.prototype.flash_menu       = 'false';
JSAR.prototype.flash_allowfullscreen = 'false';
JSAR.prototype.flash_version    = '9.0.0';

JSAR.prototype.zIndexFlash   = 0;
JSAR.prototype.zIndexCanvas  = 1;
JSAR.prototype.zIndexHTML    = 2;

JSAR.prototype.marker_lineCap       = 'butt';
JSAR.prototype.marker_lineJoin      = 'miter';
JSAR.prototype.marker_lineWidth     = '3';

JSAR.prototype.markerColors = [
    'red',      'yellow',   'lime',     'aqua',     
    'blue',     'fuchsia',  'maroon',   'olive',    
    'green',    'teal',     'navy',     'purple',   
    'black',    'gray',     'silver',   'white'
];

JSAR.prototype.drawMarkerRect = true;
JSAR.prototype.onCameraReady = function () {};
JSAR.prototype.onMarkerReady = function () {};
JSAR.prototype.onDetected    = function () {};

// private

JSAR.prototype._swfName;
JSAR.prototype._flashLayer;
JSAR.prototype._canvasLayer;
JSAR.prototype._htmlLayer;

// class variable

JSAR.Instances = 0;

// init

JSAR.prototype.init = function () {
    var area = document.getElementById(this.jsarId);
    area.style.width  = this.displayX + 'px';
    area.style.height = this.displayY + 'px';

    // unique id
    this._swfName = '_' + this.jsarId + '_swf_' + this.instanceId;

    // create object element for flash
    var dummyelem = document.createElement( 'span' );   // dummy
    dummyelem.id = this._swfName;
    area.appendChild( dummyelem );
    var flashvars = {};
    var params = {
        wmode:           this.flash_wmode,
        quality:         this.flash_quality,
        scale:           this.flash_scale,
        salign:          this.flash_salign,
        menu:            this.flash_menu,
        allowfullscreen: this.flash_allowfullscreen,
    };
    var attributes = {};
    var swf_path   = this.jsarSWF;
    if ( this.noCache ) {
        swf_path += '?'+Math.floor(Math.random()*900000+100000); // random
    }
    var inst_path  = this.installSWF;

    swfobject.embedSWF(swf_path, this._swfName, this.displayX, this.displayY,
        this.flash_version, inst_path, flashvars, params, attributes);  
    var embed = document.getElementById(this._swfName);  

    if ( embed ) {
        embed.style.position = 'absolute';
        embed.style.zIndex   = this.zIndexFlash;
        this._flashLayer = embed
    }

    // create canvas element for drawing
    var canvas = document.createElement( 'canvas' );
    if ( canvas && canvas.getContext ) {
        canvas.width    = this.displayX;
        canvas.height   = this.displayY;
        area.appendChild( canvas );
        canvas.style.position = 'absolute';
        canvas.style.zIndex   = '-1';
        canvas.style.overflow = 'hidden';

        // don't use style sheet for size
//      canvas.style.width    = this.displayX + 'px';
//      canvas.style.height   = this.displayY + 'px';
        this._canvasLayer = canvas;
    }

    // create div element for html control
    var div = document.createElement( 'div' );
    if ( div ) {
        area.appendChild( div );
        div.style.position = 'absolute';
        div.style.zIndex   = '-1';
        div.style.overflow = 'hidden';
        div.style.width    = this.displayX + 'px';
        div.style.height   = this.displayY + 'px';
        this._htmlLayer = div;
    }

    // next phase
    this.callAsync( 'callInitCamera' );
}

JSAR.prototype._log = function ( mess ) {
    if ( window.console ) {
        console.log( mess );
    }
};

JSAR.prototype.callAsync = function ( name ) {
    var self = this;
    var func = function () { self[name](); }
    window.setTimeout( func, this.asyncWait );
}

JSAR.prototype._swfObject;
JSAR.prototype.getJsarProxy = function () {
    if ( this._swfObject ) return this._swfObject;
    if (navigator.appName.indexOf("Microsoft") != -1) {
        this._swfObject = window[this._swfName]
    } else {
        this._swfObject = document[this._swfName]
    }
    return this._swfObject;
}

JSAR.prototype._cameraReady;
JSAR.prototype.callInitCamera = function () {
    var jsarProxy = this.getJsarProxy();

    if ( jsarProxy && jsarProxy.initCamera ) {
        var opt = {};
        opt.cameraFile = this.cameraFile;
        opt.captureX   = this.captureX;
        opt.captureY   = this.captureY;
        opt.displayX   = this.displayX;
        opt.displayY   = this.displayY;
        opt.enableMic  = !! this.enableMic;
        opt.smoothing  = true;
        jsarProxy.initCamera( opt );
        this._cameraReady = true;
        if ( this.onCameraReady ) {
            this.onCameraReady();
        }
        this.startLoop()
    } else {
        // try again
        this.callAsync( 'callInitCamera' );
    }
}

JSAR.prototype.setMarker = function ( patlist ) {
    if ( patlist ) this.markerFiles = patlist;
    this.callAsync( 'callSetMarker' );
}

JSAR.prototype._markerReady;
JSAR.prototype.callSetMarker = function () {
    var jsarProxy = this.getJsarProxy();

    if ( ! this.markerFiles ) this.markerFiles = [];

    if ( jsarProxy && jsarProxy.setMarker ) {
        var list = [];
        for( var i=0; i<this.markerFiles.length; i++ ) {
            list[i] = this.markerFiles[i];           // copy url
            if ( this.noCache ) {
                list[i] += '?'+Math.floor(Math.random()*900000+100000);   // random
            }
        }
        jsarProxy.setMarker( list );
        this._markerReady = true;
        if ( this.onMarkerReady ) {
            this.onMarkerReady();
        }
    } else {
        // try again
        this.callAsync( 'callSetMarker' );
    }
}

JSAR.prototype.loopStarted = false;
JSAR.prototype.startLoop = function () {
    if ( this.loopStarted ) return;
    this.loopStarted = true;
    this.callAsync( 'detectLoop' );
};

JSAR.prototype.detectLoop = function () {
    var jsarProxy = this.getJsarProxy();

    // unique global callback function
    var callback = '_' + this.jsarId + '_callback_' + this.instanceId;
    if ( ! window[callback] ) {
        var self = this;
        window[callback] = function ( arg ) {
            self.detectCallback( arg );
        };
    }

    if ( jsarProxy && jsarProxy.detectMarker ) {
        // call flash proxy
        jsarProxy.detectMarker( callback );
    } else {
        // next loop
        this.callAsync( 'detectLoop' );
    }
}

JSAR.prototype._lastDetected;
JSAR.prototype._captureStarted;
JSAR.prototype.detectCallback = function ( result ) {
    // marker object
    if ( result.detected ) {
        for( var i=0; i<result.markers.length; i++ ) {
            result.markers[i] = new JSAR.MarkerDetectorResult( result.markers[i], this );
        }
    }

    // change layers on first detection
    if ( result.detected && ! this._captureStarted ) {
        var div = this.getHTML();
        if ( div ) div.style.zIndex = this.zIndexHTML;
        var canvas = this.getCanvas();
        if ( canvas ) canvas.style.zIndex = this.zIndexCanvas;
        this._captureStarted = true;
    }

    // marker detected or lost
    if ( result.detected ) {
        if ( this.drawMarkerRect ) this.drawRect( result );
        if ( this.onDetected ) this.onDetected( result );
        this._lastDetected = true;
    } else if ( this._lastDetected ) {
        if ( this.drawMarkerRect ) this.drawRect( result );
        if ( this.onLost ) this.onLost( result );
        this._lastDetected = false;
    }

    // next loop
    this.callAsync( 'detectLoop' );
}

JSAR.prototype.drawRect = function ( result ) {
    var canvas = this.getCanvas2D();
    if ( ! canvas) return;

    if ( this._lastDetected ) this.clearCanvas();
    if ( ! result.markers ) return;
    if ( ! result.markers.length ) return;

    canvas.lineCap     = this.marker_lineCap;
    canvas.lineJoin    = this.marker_lineJoin;
    canvas.lineWidth   = this.marker_lineWidth;

    for( var i=0; i<result.markers.length; i++ ) {
        var codeid = result.markers[i].codeid;
        canvas.strokeStyle = this.markerColors[ codeid % this.markerColors.length ];
        canvas.beginPath();
        var v = result.markers[i].vertex;
        canvas.moveTo( v[3].x, v[3].y );
        for( var j=0; j<4; j++ ) {
            canvas.lineTo( v[j].x, v[j].y );
        }
        canvas.closePath();
        canvas.stroke();
    }
}

JSAR.prototype.clearCanvas = function () {
    var canvas = this.getCanvas2D();
    if ( ! canvas ) return;
    canvas.clearRect( 0, 0, this.displayX, this.displayY );
}

JSAR.prototype.clearHTML = function () {
    var div = this.getHTML();
    if ( ! div ) return;
    while ( div.lastChild ) { 
        div.removeChild( div.lastChild );
    }
}

JSAR.prototype.getFlash = function () {
    return this._flashLayer;
}

JSAR.prototype.getCanvas = function () {
    return this._canvasLayer;
}

JSAR.prototype.getCanvas2D = function () {
    if ( ! this._canvasLayer ) return;
    return this._canvasLayer.getContext('2d');
}

JSAR.prototype.getHTML = function () {
    return this._htmlLayer;
}

JSAR.MarkerDetectorResult = function (obj,jsar) {
    for( var key in obj ) {
        this[key] = obj[key];
    }
    this.jsar       = jsar;
};
JSAR.MarkerDetectorResult.prototype.getColor = function () {
    return this.jsar.markerColors[this.codeid];
};
JSAR.MarkerDetectorResult.prototype.getTop = function () {
    if ( this._top ) return this._top;
    var min = this.vertex[0].y;
    for( var i=1; i<4; i++ ) {
        if ( min > this.vertex[i].y ) min = this.vertex[i].y;
    }
    this._top = min;
    return min;
}
JSAR.MarkerDetectorResult.prototype.getBottom = function () {
    if ( this._bottom ) return this._bottom;
    var max = this.vertex[0].y;
    for( var i=1; i<4; i++ ) {
        if ( max < this.vertex[i].y ) max = this.vertex[i].y;
    }
    this._bottom = max;
    return max;
}
JSAR.MarkerDetectorResult.prototype.getLeft = function () {
    if ( this._left ) return this._left;
    var min = this.vertex[0].x;
    for( var i=1; i<4; i++ ) {
        if ( min > this.vertex[i].x ) min = this.vertex[i].x;
    }
    this._left = min;
    return min;
}
JSAR.MarkerDetectorResult.prototype.getRight = function () {
    if ( this._right ) return this._right;
    var max = this.vertex[0].x;
    for( var i=1; i<4; i++ ) {
        if ( max < this.vertex[i].x ) max = this.vertex[i].x;
    }
    this._right = max;
    return max;
}

