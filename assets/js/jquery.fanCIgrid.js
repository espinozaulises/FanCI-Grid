(function ($) {

    /*function History() {
        this._callback = function (hash) {};
    };*/

    /*$.extend(History.prototype, {

        init: function (callback) {
            this._callback = callback;
        },

        load: function (hash) {
            this._callback(hash);
        }
    });*/

    $(document).ready(function () {
        //$.history = new History(); // singleton instance
        $('a.actions-grid').tipsy({gravity: 'e'});
        $('a.sorter-grid').tipsy({gravity: 'n'});
        $('.pager .btn-group .btn').tipsy({gravity: 's'});
    });

    // -----------------------------------------------------------------
    $.fn.fancigrid = function (options) {
        var opts = $.extend({}, $.fn.fancigrid.defaults, options);

        return this.each(function () {
            $.fancigrid(this, opts);
        });
    };

    $.fancigrid = function (grid, opts) {
        if ($(grid).hasClass('fancigrid-noajax')) {
            return false;
        }

        var baseUrl = $('#url_site', grid).text();
        var gridUrl = $('#dg_url', grid).text();
        var loading = false;
        var ajaxSessionStart = false;

        $.ajaxSetup({
            cache: false,
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
        // Init history plugin
        $.history.init(callback);

        $("#btool-add").click(function (e){
            var str = $(this).attr("class");
            var patt = /no-dialog/g;
            if( !patt.test(str) ) {
                e.preventDefault();
                show_dialogo_frame($(this).attr('href'), $(this).attr('original-title'))
            }
        });

        $("#btool-search").click(function (e) {
            e.preventDefault();
            data = {};

            if ($("#str_search").val() !== '') {
                data["str_search"] = $("#str_search").val();
                strsearch = encode($("#str_search").val());
                $.history.load("/" + $('#dg_hash_init', grid).text() + "-" + $("#dg_filter_by").val() + ":" + strsearch + "-" + $("#dg_vars").text());

            } else {
                $("#dg_but_clear").click();
            }
        });

        $("#btool-clear").click(function (e) {
            e.preventDefault();
            $("#str_search").val("");
            $.history.load("/" + $('#dg_hash_init', grid).text() + "-none-" + $("#dg_vars").text());

        });

        $("a.dg_trash").click(function (e) {
            e.preventDefault();
            $.post( $(this).attr("href"), function (html) {
                $.history.load("/" + $('#dg_hash', grid).text());
            });
        });

        $('.dg_check_toggler').click(function () {
            var checkboxes = $(this).parents('table').find('.dg_check_item');
            if ($(this).is(':checked')) {
                checkboxes.attr('checked', 'true');
            } else {
                checkboxes.removeAttr('checked');
            }
            //$(this).parents('table').find('td :checkbox').attr('checked', $(this).attr('checked'));
            if ($(this).attr('checked')) {
                $(this).parents('table').find('tbody td').addClass('state-highlight');
            }
            else {
                $(this).parents('table').find('tbody td').removeClass('state-highlight');
            }
            checkButtons();
        });
        // Call custom init funciton
        initGrid();

        function callback(hash) {
            if ((hash == '' || hash == '#') && !ajaxSessionStart) {
                ajaxSessionStart = true;
                return false;
            }

            var url = gridUrl + hash;
            startLoad(grid);
            $.post(url, function (html) {
                $(grid).html(html);
                endLoad(grid);
                initGrid();
            });
        }

        function ChangeActionForm() {
            $("#form_filter").attr("action", $("#dg_url").text() + "/" + $("#dg_hash_init").text())
        }

        function checkButtons() {
            // Enable/disable buttons
            if ($('#table-grid td :checkbox:checked').length) {
                $('#btool-trash').removeClass('hide');
            }
            else {
                $('#btool-trash').addClass('hide');
            }
        }

        function decode(s) {
            return Base64.decode(s.replace(/%/g, '+').replace(/\./g, '/').replace(/~/, '='));
        }
        
        function encode(s) {
            return Base64.encode(s).replace(/\+/g, '%').replace(/\//g, '.').replace(/\=/g, '~');
        }

        function endLoad(element) {
            $(element).children('.dg-block, .dg-block-message').remove();
            $(element).css('position', $(element).data('position'));
            $(element).css('overflow', $(element).data('overflow'));
            //$(element).hideLoading()
        }

        function initGrid() {
            // Enable/disable buttons
            //checkButtons();
            //ChangeActionForm();

            //$(".dg-pagination").each(function(){
            //  $(this).attr("href", $(this).attr("href") + "/"+$(".dg_order").text()+"/"+$(".dg_order_type").text());
            //});
            /*$(".dg_check_item").each(function(){
                $(this).removeAttr('checked');
            }); */
            // Autosize de colunmas
            if ( opts.autosize ) {
                $("tbody tr:last td", grid).each(function(index){
                    var tdWidth = $(this).outerWidth();
                    $("thead th", grid).eq(index).width(tdWidth);
                    $(this).width(tdWidth);
                });
            }

            $("#dg-bot-clean").click(function (e) {
                e.preventDefault();
                $("#str_search").val("");
                callback("/" + $('#dg_hash_init').text());
                //return false;
            });

            // Init inner links
            $('a.dg-pagination', grid).click(function () {
                var url = $(this).attr('href');
                $.history.load(this.href.replace(gridUrl, ''));
                return false;
            });
            // Init sorter links
            $('li a.item', grid).click(function () {
                var url = $(this).attr('href');
                $.history.load(this.href.replace(gridUrl, ''));
                return false;
            });
            // Init page size change
            $('select[name=limit]', grid).change(function () {
                var hash = $('.cg-params', grid).text() + $(this).val() + '/0/' + $('.cg-order-string', grid).text() + '/' + $('.cg-filter-string', grid).text();
                $.history.load(hash);
            });

            // Init row selection    
            $('tbody tr:has(:checkbox) td', grid).click(function (e) {

                if ($(e.target).attr('type') !== 'checkbox') {
                    var checkbox = $(this).parents('tr').find('td.fg-select :checkbox');
                    checkbox.attr('checked', !checkbox.attr('checked'));
                }
                if ($('td :checkbox').length == $('td :checkbox:checked').length) {
                    $('th.fg-select :checkbox').attr('checked', 'checked');
                }
                else {
                    $('th.fg-select :checkbox').attr('checked', '');
                }
                checkButtons();
            });

            $("tr", grid).click(function () {

                $(this).children("td").toggleClass("state-highlight");
            });
            // Toggle the dropdown menu's
            $(".drop-arrow .filter-arrow").click(function () {
                $('.drop-arrow-slider').css('display','none');
                
                $('#ul-' + $(this).attr("id")).slideToggle('fast');
                $(this).parent().find('span.toggle').toggleClass('active');
                //$(this).children("ul").fadeIn(300);
                return false;
            });

            // Close open dropdown slider/s by clicking elsewhwere on page
            $(document).bind('click', function (e) {
                if (e.target.id != $('.drop-arrow').attr('class')) {
                    $('.drop-arrow-slider').slideUp();
                    $('span.toggle').removeClass('active');
                }
            }); // END document.bind
            opts.load(grid);
        }
        
        function show_dialogo_frame(href, title) {
            var horizontalPadding = 5;
            var verticalPadding = 5;
            refDialog = $('<iframe id="site" src="' + href + '" />').dialog('destroy').dialog({
                title: (title) ? title : 'FactuCEMPI',
                autoOpen: true,
                hide: "highlight",
                width: 700,
                height: 420,
                modal: true,
                position: 'center',
                resizable: false,
                autoResize: true,
                closeOnEscape: true,
            }).width(690 - horizontalPadding).height(375 - verticalPadding);
        }
        
        function startLoad(element, anim) {
            if (anim === undefined) anim = true;
            $(element).data('position', $(element).css('position'));
            $(element).data('overflow', $(element).css('overflow'));
            $(element).css('position', 'relative');
            $(element).css('overflow', 'hidden');
            $('<div class="dg-block ui-widget-overlay"><div class="dg-block-message ui-loading">' + $('.dg-lang-loading-message').text() + '</div></div>').css({
                top: 0 + $(element).scrollTop(),
                left: 0 + $(element).scrollLeft(),
                width: $(element).outerWidth(),
                height: $(element).outerHeight()
            }).appendTo($(element));
            //$(element).showLoading();
        }

        var Base64 = {
            // private property
            _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

            // public method for encoding
            encode: function (input) {
                var output = "";
                var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
                var i = 0;
                input = Base64._utf8_encode(input);
                while (i < input.length) {
                    chr1 = input.charCodeAt(i++);
                    chr2 = input.charCodeAt(i++);
                    chr3 = input.charCodeAt(i++);
                    enc1 = chr1 >> 2;
                    enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                    enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                    enc4 = chr3 & 63;
                    if (isNaN(chr2)) {
                        enc3 = enc4 = 64;
                    } else if (isNaN(chr3)) {
                        enc4 = 64;
                    }
                    output = output + this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) + this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
                }
                return output;
            },

            // public method for decoding
            decode: function (input) {
                var output = "";
                var chr1, chr2, chr3;
                var enc1, enc2, enc3, enc4;
                var i = 0;
                input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
                while (i < input.length) {
                    enc1 = this._keyStr.indexOf(input.charAt(i++));
                    enc2 = this._keyStr.indexOf(input.charAt(i++));
                    enc3 = this._keyStr.indexOf(input.charAt(i++));
                    enc4 = this._keyStr.indexOf(input.charAt(i++));

                    chr1 = (enc1 << 2) | (enc2 >> 4);
                    chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                    chr3 = ((enc3 & 3) << 6) | enc4;

                    output = output + String.fromCharCode(chr1);

                    if (enc3 != 64) {
                        output = output + String.fromCharCode(chr2);
                    }
                    if (enc4 != 64) {
                        output = output + String.fromCharCode(chr3);
                    }
                }
                output = Base64._utf8_decode(output);
                return output;

            },

            // private method for UTF-8 encoding
            _utf8_encode: function (string) {
                string = string.replace(/\r\n/g, "\n");
                var utftext = "";
                for (var n = 0; n < string.length; n++) {
                    var c = string.charCodeAt(n);
                    if (c < 128) {
                        utftext += String.fromCharCode(c);
                    }
                    else if ((c > 127) && (c < 2048)) {
                        utftext += String.fromCharCode((c >> 6) | 192);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }
                    else {
                        utftext += String.fromCharCode((c >> 12) | 224);
                        utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }
                }
                return utftext;
            },

            // private method for UTF-8 decoding
            _utf8_decode: function (utftext) {
                var string = "";
                var i = 0;
                var c = c1 = c2 = 0;
                while (i < utftext.length) {
                    c = utftext.charCodeAt(i);

                    if (c < 128) {
                        string += String.fromCharCode(c);
                        i++;
                    }
                    else if ((c > 191) && (c < 224)) {
                        c2 = utftext.charCodeAt(i + 1);
                        string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                        i += 2;
                    }
                    else {
                        c2 = utftext.charCodeAt(i + 1);
                        c3 = utftext.charCodeAt(i + 2);
                        string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                        i += 3;
                    }

                }
                return string;
            }
        }
    };

    $.fn.datagrid.defaults = {
        load: function (grid) {
            autosize: true
        }
    };

})(jQuery);