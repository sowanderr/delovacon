;
(function () {

    if (window.frameCacheVars !== undefined) {
        BX.addCustomEvent("onFrameDataReceived", function (json) {
            var obj = new BXmakerGeoIPMessage();
            if (!!window.BXmakerGeoIP === false) {
                setTimeout(function () {
                    (!!window.BXmakerGeoIP && obj.init());
                }, 100);
            }
            else {
                obj.init();
            }
        });
    } else {
        BX.ready(function () {
            var obj = new BXmakerGeoIPMessage();
            if (!!window.BXmakerGeoIP === false) {
                setTimeout(function () {
                    (!!window.BXmakerGeoIP && obj.init());
                }, 100);
            }
            else {
                obj.init();
            }
        });
    }

})();

if (!!window.BXmakerGeoIPMessage === false) {
    var BXmakerGeoIPMessage = function () {
        this.box = null;
        this.aim = null;
    };

    BXmakerGeoIPMessage.prototype.init = function (htmlClass, aimClass) {

        var that = this;

        if (!!window.$bxm == false && !!console.error) {
            console.error('bxmaker.geoip.message - need jQuery');
            return true;
        }

        $bxm('.c_bxmaker_geoip_message_default_box:not(.init_ok)').each(function(){
            $bxm(this).addClass('init_ok');
            that.event($bxm(this));
        });


    };

    BXmakerGeoIPMessage.prototype.event = function (box) {
        var box = box;

        $bxm(document).on("bxmaker.geoip.onAfterSaveCity", function (e, data) {
            box.addClass('preloader');

            $bxm(document).trigger('bxmaker.geoip.onBeforeGetDelivery');

            $bxm.ajax({
                url: '/',
                type: 'POST',
                dataType: 'json',
                data: {
                    sessid: BX.bitrix_sessid(),
                    module: 'bxmaker.geoip',
                    method: 'getMessage',
                    type: box.attr('data-type'),
                    template: box.attr('data-template')
                },
                error: function (r) {
                    window.BXmakerGeoIP.log(' getMessage: error connection', true);
                    window.BXmakerGeoIP.log(r, true);

                    box.removeClass('preloader');
                    $bxm(document).trigger('bxmaker.geoip.onAfterGetMessage', r);
                },
                success : function(r){
                    if(!!r.response)
                    {
                        window.BXmakerGeoIP.log(' getMessage: success');
                        box.html(r.response.html);

                    }
                    else if(!!r.error)
                    {
                        window.BXmakerGeoIP.log(' getMessage: error', true);
                        window.BXmakerGeoIP.log(r, true);
                    }

                    box.removeClass('preloader');
                    $bxm(document).trigger('bxmaker.geoip.onAfterGetMessage', r);
                }
            })


        });
    }


}
