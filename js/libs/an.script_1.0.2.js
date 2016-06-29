/**
 The script implements a control HTML5 element canvas. Simplified realization of animation or static graphs,
 and some event-control model for "click", "mousemove", "keydown" and "keyup"
 */
(function(window){

    'use strict';

    var root = {
        version:    '1.0.2',
        selector:   null,
        width:      0,
        height:     0,
        fps:        null,
        canvas:     null,
        context:    null,
        frame:      0,
        image:      {},
        graphic:    {},
        glob:       {},
        options:    {},
        mouse:      { x:0, y:0 },
        mouseClick: { x:0, y:0 },
        keydownCode:null,
        keyupCode:  null,
        interval:  null,
        extensions: []
    };

    var Extension = function(func){
        root.extensions.push(func);
    };

    var An = function(options,p1,p2,p3)
    {
        if(!(this instanceof An))
            return new An(options,p1,p2,p3);
        
        var root = this;
        if(arguments.length > 2 && arguments[1] > 0)
            options = {selector:arguments[0],width:arguments[1],height:arguments[2],fps:arguments[3]};

        if(!options || !options.selector || typeof options !== 'object')
            return;

        var defaultOption = {
            selector:null,
            width:600,
            height:400,
            fps:(parseInt(options.fps) > 0) ? parseInt(options.fps) : 0,
            autoStart:true,
            autoClear:true,
            enableEventClick:true,
            enableEventMouseMovie:false,
            enableEventKeys:false
        };

        // root.options CanvasRenderingContext2D
        root.options = Util.objMerge(defaultOption, options);
        options = defaultOption = null;
        root.selector = root.options.selector;
        root.canvas = document.querySelector(root.selector);
        root.canvas.width = root.width = root.options.width || 600;
        root.canvas.height = root.height = root.options.height || 400;
        root.context = this.canvas.getContext('2d');
        root.fps = root.options.fps;
        root.lists = {};
        root.lists.stages = {};
        root.lists.events = [];
        root.lists.scenes = [];
        root.lists.scenesTemp = [];

        // initialize extensions
        if(root.extensions.length > 0){
            for(var ei = 0; ei < root.extensions.length; ei ++)
                if(typeof root.extensions[ei] === 'function') root.extensions[ei].call(root, root);
        }

        // It catches the mouse movement on the canvas, and writes changes root.mouse
        if(root.options.enableEventMouseMovie){
            root.canvas.addEventListener('mousemove', function(event){
                root.mouse = Util.getMouseCanvas(root.canvas, event);
            });
        }

        // It catches the mouse clicks on the canvas, and writes changes root.mouseClick
        if(root.options.enableEventClick){
            root.canvas.addEventListener('click', function(event)
            {
                root.mouseClick = Util.getMouseCanvas(root.canvas, event);
                if(root.lists.events.click && typeof root.lists.events.click === 'object') {
                    var eventsClicks = root.lists.events.click;
                    for(var key in eventsClicks ){
                        if(
                            eventsClicks[key].rectangle[0] < root.mouseClick.x &&
                            eventsClicks[key].rectangle[1] < root.mouseClick.y &&
                            eventsClicks[key].rectangle[0]+eventsClicks[key].rectangle[2] > root.mouseClick.x &&
                            eventsClicks[key].rectangle[1]+eventsClicks[key].rectangle[3] > root.mouseClick.y
                        ){
                            eventsClicks[key].callback.call(root, event, eventsClicks[key].rectangle);
                        }
                    }
                }
            });
        }


        /**
         * Draw round rectengle
         * @param x
         * @param y
         * @param width
         * @param height
         * @param radius
         */
        root.context.rectRound = function(x, y, width, height, radius){
            radius = radius || 5;
            root.context.beginPath();
            root.context.moveTo(x + radius, y);
            root.context.arcTo(x + width, y, x + width, y + height, radius);
            root.context.arcTo(x + width, y + height, x, y + height, radius);
            root.context.arcTo(x, y + height, x, y, radius);
            root.context.arcTo(x, y, x + width, y, radius);
        };

        /**
         * Clear shadow params (shadowOffsetX,shadowOffsetY,shadowBlur)
         */
        root.context.clearShadow = function(){root.context.shadowOffsetX = root.context.shadowOffsetY = root.context.shadowBlur = 0;};

        if(!root.context.ellipse){
            /**
             * Draw ellipse - cross-browser function
             * @param x
             * @param y
             * @param radiusX
             * @param radiusY
             * @param rotation
             * @param startAngle
             * @param endAngle
             * @param anticlockwise
             */
            root.context.ellipse = function(x, y, radiusX, radiusY, rotation, startAngle, endAngle, anticlockwise){
                root.context.save();
                root.context.beginPath();
                root.context.translate(x, y);
                root.context.rotate(rotation);
                root.context.scale(radiusX / radiusY, 1);
                root.context.arc(0, 0, radiusY, startAngle, endAngle, (anticlockwise||true));
                root.context.restore();
                root.context.stroke();
                root.context.closePath();
            }
        }

        /**
         * Draw shadow for all elements on scene
         * @param x
         * @param y
         * @param blur
         * @param color
         */
        root.context.shadow = function (x,y,blur,color){
            root.context.shadowOffsetX = x;
            root.context.shadowOffsetY = y;
            root.context.shadowBlur = blur;
            root.context.shadowColor = color;
        };

        /**
         * Added callback for event "keydown" by "keyCode"
         * @param {Number} keyCode
         * @param {Function} callback - callback on event
         */
        this.addEventKeydown = function(keyCode, callback)
        {
            if(root.lists.events.keydown == null) root.lists.events.keydown = {};
            root.lists.events.keydown[keyCode] = {keyCode: keyCode, callback: callback};
        };

        /**
         * Added callback for event "keyup" by "keyCode"
         * @param {Number} keyCode
         * @param {Function} callback - callback on event
         */
        this.addEventKeyup = function(keyCode, callback)
        {
            if(root.lists.events.keyup == null) root.lists.events.keyup = {};
            root.lists.events.keyup[keyCode] = {keyCode: keyCode, callback: callback};
        };


        /**
         * Adds a callback for the event click on a certain area: rectangle = [x,y,width,height]
         * @param {Array} rectangle - [x, y, width, height]
         * @param {Function} callback - callback on event
         */
        this.addEventClick = function(rectangle, callback)
        {
            if(root.lists.events.click == null) root.lists.events.click = {};
            var eventItem = rectangle.join('_');
            if(root.lists.events.click[eventItem] == null)
                root.lists.events.click[eventItem] = {rectangle: rectangle, callback: callback};
        };


        /**
         * Removes the callback onclick event appointed above by this.addEvent Click,
         * specific area: rectangle = [x,y,width,height]
         * @param {Array} rectangle - [x, y, width, height]
         */
        this.removeEventClick = function(rectangle){
            var item = rectangle.join('_');
            if(root.lists.events.click != null && root.lists.events.click[item] != null)
                delete root.lists.events.click[item];
        };


        /**
         * It renders the scene assignments,
         * or if the specified parameter name - renders the stage by name
         * @param name - stage name
         */
        this.render = function(name)
        {
            if(name !== undefined && typeof name === 'string')
                this.applyStage(name);

            if(root.options.autoStart)
                this.play();
        };


        /**
         * Stop animation
         */
        this.stop = function(){
            if( root.interval !== null ){
                clearInterval(root.interval);
                root.interval = null;
            }
        };

        /**
         * Play animation
         */
        this.play = function(){
            if(root.fps > 0 && root.interval === null) {
                drawFrame();
                root.interval = setInterval(drawFrame, 1000 / root.fps);
            } else
                drawFrame()
        };

        /**
         * Clear canvas area
         */
        root.clear = function(){
            root.context.clearRect(0, 0, root.width, root.height);
        };

        /**
         * It clears the canvas to render the new stage
         */
        root.clearStage = function(){
            root.lists.scenes = root.lists.scenesTemp = root.lists.events = [];
        };

        /**
         * Added scene
         * @param {Object} obj
         * @param {Number} obj.index - deeps scene, option march on z-index
         * @param {Function} obj.runner - function run every time relatively root.fps
         * @returns {An}
         */
        this.scene = function(obj) {
            if(obj !== null) {
                if(typeof obj === 'function') {
                    root.lists.scenesTemp.push({runner:obj});
                } else if(typeof obj === 'object' && typeof obj.runner === 'function') {
                    root.lists.scenesTemp.push(obj);
                }
            }
            return this;
        };

        /**
         * Added stage
         * @param {String} name - name of stage, rendering is defined by name
         * @param {Object} obj - Object. is scene object
         * @param {Number} obj.index - deeps scene, option march on z-index
         * @param {Function} obj.runner - function run every time relatively root.fps
         */
        this.stage = function(name, obj)
        {
            if(root.lists.stages[name] == null)
                root.lists.stages[name] = [];

            root.lists.stages[name].push(obj);
        };


        /**
         * Apply renderer for the scene by the name
         * @param {String} name
         * @param {Boolean} clear
         */
        this.applyStage = function(name, clear)
        {
            if(clear !== false)
                root.clearStage();

            if(Array.isArray(root.lists.stages[name])){
                for(var i = 0; i < root.lists.stages[name].length; i ++){
                    this.scene(root.lists.stages[name][i]);
                }
            }
        };


        /**
         * Resize element Canvas on full page, or by params
         * @param {Number} width - default full window width
         * @param {Number} height - default full window height
         */
        this.resizeCanvas = function(width,height) {
            root.canvas.style.position = 'absolute';
            root.canvas.width = root.width = width || window.innerWidth;
            root.canvas.height = root.height = height || window.innerHeight;
        };

        /**
         * Loading Resource Image.
         * Object imgs:
         * key - is the name for the access, assigned after loading
         * value - is the URL of the resource to load
         * @param {Object} imgs - { key : value, key : value, ...  }
         * @param {Function} callback
         */
        this.imageLoader = function(imgs, callback) {
            if(!imgs && typeof imgs !== 'object') return;
            var length = an.u.objLength(imgs);
            var images = {};
            var iterator = 0;
            for(var name in imgs){
                var eImg = document.createElement('img');
                eImg.src = imgs[name];
                eImg.name = name;
                eImg.onload = function(e){
                    images[this.name] = this;
                    iterator ++;
                    if(iterator == length) {
                        root.image = Util.objMerge(root.image,images);
                        callback.call(root, root.image, root.context);
                    }
                };
            }
        };


        // - - - - - - - - - - - - - - - - - - - - - - - - -
        // insides methods
        // - - - - - - - - - - - - - - - - - - - - - - - - -

        var drawFrame = function()
        {
            root.frame ++;

            if(root.lists.scenes.length == 0 && root.lists.scenesTemp.length > 0){
                root.lists.scenes = root.lists.scenesTemp.sort(function(one, two){
                    return (one['index'] > two['index']) ? true : false;
                });
                delete root.lists.scenesTemp;
            }

            if(root.options.autoClear === true)
                root.clear();

            root.lists.scenes.forEach(function(item){
                try{
                    root.context.beginPath();
                    root.context.save();
                    item.runner.call(item, root.context, root);
                    root.context.restore();
                }catch(error){
                    console.error(error.message);
                }
            });
        };


        if(root.options.enableEventKeys){
            window.addEventListener('keydown', function(event){
                root.keydownCode = event.keyCode;
                if(root.lists.events.keydown != null && typeof root.lists.events.keydown[event.keyCode] === 'object'){
                    var e = root.lists.events.keydown[event.keyCode];
                    e.callback.call(root, event);
                }
            });
            window.addEventListener('keyup', function(event){
                root.keyupCode = event.keyCode;
                if(root.lists.events.keyup != null && typeof root.lists.events.keyup[event.keyCode] === 'object'){
                    var e = root.lists.events.keyup[event.keyCode];
                    e.callback.call(root, event);
                }
            });
        }

    };


    // - - - - - - - - - - - - - - - - - - - - - - - - -
    // Graphics static methods
    // - - - - - - - - - - - - - - - - - - - - - - - - -

    /**
     * Debug Panel, show dynamic information: load, performance, frames ...
     * Panel size - full width and 30px height.
     * Position - default on top
     * @param  {Object|?} option - params object
     * @param  {String} option.bgColor - background color of panel, default = #DDDDDD
     * @param  {String} option.textColor - color of panel text, default = #000000
     * @param  {Boolean} option.countEvents - show the number of active events
     * @param  {Boolean} option.countScenes - show the total number of scenes
     * @param  {Boolean} option.countStages - show the total number of stages
     * @param  {Number} option.load - loading panel, default = 6%
     * @param  {Object} option.margin - params position, margin of panel
     * @param  {Number} option.margin.x - margin x
     * @param  {Number} option.margin.y - margin y
     * @param  {Object} option.padding - params padding text inside panel
     * @param  {Number} option.padding.x - padding x
     * @param  {Number} option.padding.y - padding y
     */
    root.graphic.debugPanel = function(option){

        option = (option) ? option : {};

        if(root.options.devPanel === undefined){
            root.options.devPanel = {
                bgColor:option.bgColor||'#DDDDDD',
                textColor:option.textColor||'#000000',
                iterator:0,
                timeStart:new Date().getTime(),
                timeLast:0,
                percent:0,
                countEvents:(option.countEvents===false)?false:true,
                countScenes:(option.countScenes===false)?false:true,
                countStages:(option.countStages===false)?false:true,
                load:(option.load && option.load !== 0)?option.load:6,
                margin:option.margin||{x:0,y:0},
                padding:option.padding||{x:3,y:3}
            };
        }
        var opt = root.options.devPanel;
        var textX = opt.padding.x + opt.margin.x;
        var textY = opt.padding.y + opt.margin.y;

        root.context.fillStyle = opt.bgColor;
        root.context.fillRect(opt.margin.x,opt.margin.y,root.width,30);
        root.context.font = 'bold 12px/12px Arial';
        root.context.textBaseline = 'top';
        root.context.fillStyle = opt.textColor;
        root.context.fillText('frames: ' + opt.iterator,textX,textY);
        root.context.fillText('seconds: ' + parseInt((new Date().getTime() - opt.timeStart) / 1000), textX,textY + 12);

        var timeNow = (new Date).getTime();
        var ftp = (timeNow - opt.timeLast)/1000;

        if(opt.iterator % 60 == 0){
            var p = parseInt(parseInt(1/ftp) *  100 / root.fps) + opt.load;
            opt.percent = ((p>100)?100:p) + '%';
        }

        root.context.fillStyle = opt.textColor;
        root.context.font = "12px/14px Arial";
        root.context.fillText("FPS: " + parseInt(1/ftp) + '/' + root.fps, 100+textX, textY+6);
        root.context.fillText(opt.percent+'', 170+textX, textY+6);
        if(opt.countEvents)
            root.context.fillText("Events: " + Util.objLength(root.lists.events.click), 230+textX, textY+6);
        if(opt.countScenes)
            root.context.fillText("Scenes: " + root.lists.scenes.length, 320+textX, textY+6);
        if(opt.countStages)
            root.context.fillText("Stages: " + Util.objLength(root.lists.stages), 410+textX, textY+6);

        opt.timeLast = timeNow;
        opt.iterator ++;

    };


    // - - - - - - - - - - - - - - - - - - - - - - - - -
    // Utilities static methods
    // - - - - - - - - - - - - - - - - - - - - - - - - -


    var Util = {};
    /**
     * Cloned object
     * @param {Object} obj
     * @returns {Object}
     */
    Util.objClone = function(obj){
        if (obj === null || typeof obj !== 'object') return obj;
        var temp = obj.constructor();
        for (var key in obj)
            temp[key] = Util.objClone(obj[key]);
        return temp;
    };
    /**
     * Merge object into objectBase. Object objectBase will be modified!
     * @param {Object} objectBase
     * @param {Object} object
     * @returns {*}
     */
    Util.objMerge = function(objectBase,object){
        for(var key in object){
            objectBase[key] = object[key];
        }
        return objectBase;
    };
    Util.objMergeNotExists = function(objectBase,object){
        for(var key in object)
            if(objectBase[key] === undefined)
                objectBase[key] = object[key];
        return objectBase;
    };
    Util.objMergeOnlyExists = function(objectBase,object){
        for(var key in object)
            if(objectBase[key] !== undefined)
                objectBase[key] = object[key];
        return objectBase;
    };
    /**
     * Returns a random integer between min, max, unless specified from 0 to 100
     * @param {number} min
     * @param {number} max
     * @returns {number}
     */
    Util.rand = function(min,max){
        min = min||0; max = max||100;
        return Math.floor(Math.random() * (max - min + 1) + min);
    };
    /**
     * Random color. Returns a string HEX format color.
     * @returns {string}
     */
    Util.randColor = function(){
        var letters = '0123456789ABCDEF'.split(''),
            color = '#';
        for (var i = 0; i < 6; i++ )
            color += letters[Math.floor(Math.random() * 16)];
        return color;
    };
    /**
     * Converts degrees to radians
     * @param {number} deg - degrees
     * @returns {number}
     */
    Util.degreesToRadians = function(deg){return (deg * Math.PI) / 180;};
    /**
     * Converts radians to degrees
     * @param {number} rad - radians
     * @returns {number}
     */
    Util.radiansToDegrees = function(rad){return (rad * 180) / Math.PI;};
    /**
     * Calculate the number of items in e "obj"
     * @param {Object} obj
     * @returns {number}
     */
    Util.objLength = function(obj){
        var it = 0;
        for(var k in obj) it ++;
        return it;
    };
    /**
     * Calculate the distance between points
     * @param {Object} p1
     * @param {number} p1.x
     * @param {number} p1.y
     * @param {Object} p2
     * @param {number} p2.x
     * @param {number} p2.y
     * @returns {number}
     */
    Util.distanceBetween = function(p1,p2){
        var dx = p2.x-p1.x;
        var dy = p2.y-p1.y;
        return Math.sqrt(dx*dx + dy*dy);
    };
    /**
     * Returns the coordinates of the mouse on any designated element
     * @param {Object} element
     * @param {Object} event
     * @returns {{x: number, y: number}}
     */
    Util.getMouseElement = function(element, event) {
        var x = event.pageX - element.offsetLeft;
        var y = event.pageY - element.offsetTop;
        return {x: x, y: y};
    };
    /**
     * Returns the coordinates of the mouse on canvas element
     * @param {Object} canvas
     * @param {Object} event
     * @returns {{x: number, y: number}}
     */
    Util.getMouseCanvas = function(canvas, event){
        var rect = canvas.getBoundingClientRect();
        return {
            x: event.clientX - rect.left,
            y: event.clientY - rect.top
        };
    };




    An.prototype = Object.create(root);
    An.prototype.constructor = An;
    An.prototype.u = Util;
    window.An = An;
    window.An.Util = Util;
    window.An.Extension = Extension;

})(window);
