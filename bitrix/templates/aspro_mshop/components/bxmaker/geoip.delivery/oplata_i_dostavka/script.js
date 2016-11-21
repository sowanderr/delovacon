;
(function () {

    if (window.frameCacheVars !== undefined) {
        BX.addCustomEvent("onFrameDataReceived", function (json) {
            var obj = new BXmakerGeoIPDelivery();
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
            var obj = new BXmakerGeoIPDelivery();
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

if (!!window.BXmakerGeoIPDelivery === false) {
    var BXmakerGeoIPDelivery = function () {
        this.box = null;
        this.aim = null;
    };

    BXmakerGeoIPDelivery.prototype.init = function (htmlClass, aimClass) {

        if (!!window.jQuery == false && !!console.error) {
            console.error('bxmaker.geoip.delivery - need jQuery');
            return true;
        }

      

        this.box = $bxm('.c-bxmaker_geoip_delivery_default_box:not(.init_ok)').eq(0);
        if (!this.box.length) {
            return false;
        }
        this.box.addClass('init_ok');


        this.event();


    };

    BXmakerGeoIPDelivery.prototype.event = function () {
        var that = this;

        $bxm(document).on("bxmaker.geoip.onAfterSaveCity", function (e, data) {
            that.box.addClass('preloader');

            $bxm(document).trigger('bxmaker.geoip.onBeforeGetDelivery');

            $bxm.ajax({
                url: '/',
                type: 'POST',
                dataType: 'json',
                data: {
                    sessid: BX.bitrix_sessid(),
                    module: 'bxmaker.geoip',
                    method: 'getDelivery'
                },
                error: function (r) {
                    window.BXmakerGeoIP.log(' getDelivery: error connection', true);
                    window.BXmakerGeoIP.log(r, true);

                    that.box.removeClass('preloader');
                    $bxm(document).trigger('bxmaker.geoip.onAfterGetDelivery', r);
                },
                success : function(r){
                    if(!!r.response)
                    {
                        window.BXmakerGeoIP.log(' getDelivery: success');
                        that.box.html(r.response.html);
                        that.box.find('.bxmaker_geoip_epilog_city_name').text(window.BXmakerGeoIP.getCity());

                    }
                    else if(!!r.error)
                    {
                        window.BXmakerGeoIP.log(' getDelivery: error', true);
                        window.BXmakerGeoIP.log(r, true);
                    }

                    that.box.removeClass('preloader');
                    $bxm(document).trigger('bxmaker.geoip.onAfterGetDelivery', r);
                }
            })


        });
    }


}
