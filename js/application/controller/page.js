if (App.namespace) {
    App.namespace('Controller.Page', function (App) {
        /**
         * @namespace App.Controller.Page
         */
        var ctrl = {},

            node = {},

            Linker = App.Extension.Linker,
            Dom = App.Extension.Dom,

            Error = App.Action.Error;


        ctrl.construct = function () {

            App.domLoaded(build);

        };

        function build() {

            // query base HTML elements in the page
            node = App.node({
                app: App.query('#app'),
                appContent: App.query('#app-content')
            });


            //кнопка загрузить логотип "upload"
            document.getElementById("uploadLogoBtn").onchange = function () {
                var file = document.getElementById('uploadLogoBtn').files[0];
                $('#uploadimg').show();
                uploadLogo(file, function (response) {
                    try {
                        var r = JSON.parse(response);
                        var file = r[0];
                        $(".uploadedfiles ul").append('<li>' + file.name + ' ' + sizeDefinitionString(file.size) + '<input type="hidden" name="upload-files[]" value="' + file.id + '"></li>');
                        var usr_name = $('head').attr('data-user');
                        // $("#project_logo").attr('data-src', usr_name + '/files/ProjectLogo/' + file.name);
                        App.Action.Api.request('logoBaseEncode', function (response) {
                            if (response.requesttoken) {
                                app.requesttoken = response.requesttoken;

                                $("#project_logo").attr('src', response.src);
                            }
                        },{'logo_src' :  usr_name + '/files/ProjectLogo/' + file.name});

                        //вставка изображений
                        $('.uploadedfiles>ul>li').click(function (event) {
                            event.preventDefault();
                            // $("#project_logo").attr('data-src', usr_name + '/files/ProjectLogo/' + file.name);
                            App.Action.Api.request('logoBaseEncode', function (response) {
                                if (response.requesttoken) {
                                    app.requesttoken = response.requesttoken;

                                    $("#project_logo").attr('src', response.src);
                                }
                            },{'logo_src' :  usr_name + '/files/ProjectLogo/' + file.name});
                        });
                    } catch (e) {
                    }
                });
                $('#uploadimg').hide();

            }
            //кнопка выбрать логотип "choose"
            $("#ajax_button_show_files").click(
                function (event) {
                    event.preventDefault();
                    if (!$(".list_files>ul").html()) {
                        $(this).text('Close');
                        $('#uploadimg').show();
                        App.Action.Api.request('getlogo', function (response) {
                           
                            if (response.requesttoken) {
                                app.requesttoken = response.requesttoken;
                                var list_img = '';
                                for (var i = 0; i < response.files.length; i++) {
                                    list_img += '<li>' + response.files[i].name + '</li>'
                                }
                                if(!list_img){
                                    list_img+='<p>у Вас нет изображений</p>';
                                }
                                $('.list_files>ul').html(list_img);
                                //вставка изображений со списка сохраненных
                                $('.list_files>ul>li').click(function (event) {
                                    event.preventDefault();
                                    var usr_name = $('head').attr('data-user');
                                    var logo_name = $(this).html();
                                    // $("#project_logo").attr('data-src',  usr_name + '/files/ProjectLogo/' + logo_name);
                                    App.Action.Api.request('logoBaseEncode', function (response) {
                                        if (response.requesttoken) {
                                            app.requesttoken = response.requesttoken;
                                            $("#project_logo").attr('src', response.src);
                                        }
                                    },{'logo_src' :   usr_name + '/files/ProjectLogo/' + logo_name});
                                });
                            }
                        });
                        $('#uploadimg').hide();
                    } else {
                        $(".list_files>ul").html('');
                        $(this).text('Choose');
                    }
                }
            );


            //дата-пикер
            $('input.datetime').datetimepicker({
                dateFormat: 'dd.mm.yy',
                timeFormat: "HH:mm"

            });
            //canvas animation

            d1 = drawElips('#elips1');
            d2 = drawElips('#elips3');
            d3 = drawCircle('#elips2');
            d4 = drawCircle('#elips4');
            d4 = drawCircle('#elips5');
            d4 = drawCircle('#elips6');
            d4 = drawCircle('#elips7');
            d4 = drawCircle('#elips8');

            //отправка параметров дашборда
            $("form").submit(function (event) {
                event.preventDefault();
                var form = Util.formData(this, true);
                form.logo = jQuery('#project_logo').attr('src');
                App.Action.Api.request('saveall', function (response) {
                    if (response.requesttoken) {
                        App.requesttoken = response.requesttoken;
                    }
                }, {'form': form});
            });


        }


        return ctrl;

    });
//отправка логотипа на загрузку
    function uploadLogo(file, callback) {
        var fd = new FormData();
        var success = false;
        fd.append('files[]', file);
        fd.append('requesttoken', $('head').attr('data-requesttoken'));
        fd.append('dir', '/');
        fd.append('file_directory', 'ProjectLogo');

        $.ajax({
            url: "/index.php/apps/files/ajax/upload.php",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            success: function (response) {
                callback.call({}, response);
                //success = response;
                console.log(response);
            },
            error: function (jqXHR, textStatus, errorMessage) {
                console.log(errorMessage); // Optional
            }
        });
        return success;
    }

    //округляние размера в КБ, МБ, ГБ
    function sizeDefinitionString(size) {
        if (size < 1) {
            return '0 bytes';
        }

        var a = {1099511628648: 'TB', 1073741824: 'GB', 1048576: 'MB', 1024: 'kB', 1: "B"};

        var r = 0;
        var k = pk = 0;
        for (var key in a) {
            k = key;
            var d = size / key;
            if (r > 0 && d < r && d >= 1 && d < 1024) {
                r = Math.round(d * 100) / 100;
                break;
            }
            else if (r > 0 && d < 1) {
                d = size / pk;
                r = Math.round(d * 100) / 100;
                k = pk;
                break;
            }
            else {
                pk = k;
                r = d;
            }
        }
        return r + ' ' + a[k];
    }

    function drawElips(selector) {
        var persent = $(selector).attr('data-info');
        var params = {
            selector: selector,
            width: 50,
            height: 50,
            fps: 120,
            autoClear: false
        };


        var an = new An(params);


        an.scene({
            index: 1,
            pic: [
                [0, 100, '#F2F2F2'],
                [0, persent, '#0FD200']
            ],
            picDraw: 0,
            angle: 0,
            runner: function (ctx) {
                var params = this.pic[this.picDraw];

                ctx.beginPath();
                ctx.strokeStyle = params[2];
                ctx.lineWidth = 3;

                //x, y, radiusX, radiusY, rotation, startAngle, endAngle, anticlockwise
                ctx.ellipse(25, 25, 20, 20, -an.u.degreesToRadians(90), 0, an.u.degreesToRadians(360) * this.angle / 100, false);

                if (this.angle > params[1] && this.picDraw == 0) {
                    this.picDraw = 1;
                    this.angle = params[0];
                } else if (this.angle > params[1] && this.picDraw > 0) {
                    an.stop();
                }
                else this.angle += 1;

                ctx.stroke();
                ctx.closePath();
            }
        });
        an.render();
        return an;
    }

    function drawCircle(selector) {

        var params = {
            selector: selector,
            width: 50,
            height: 50,
            fps: 40,
            autoClear: false
        };

        var an = new An(params);
        an.scene({
            index: 1,
            angle: 0,
            runner: function (ctx) {
                ctx.beginPath();
                ctx.strokeStyle = '#F2F2F2';
                ctx.lineWidth = 3;

                //x, y, radiusX, radiusY, rotation, startAngle, endAngle, anticlockwise
                ctx.ellipse(25, 25, 20, 20, -an.u.degreesToRadians(90), 0, an.u.degreesToRadians(360) * this.angle / 100, false);
                if (this.angle > 100) an.stop();
                else this.angle += 3;
                ctx.stroke();
                ctx.closePath();
            }
        });
        an.render();
        return an;
    }


}

