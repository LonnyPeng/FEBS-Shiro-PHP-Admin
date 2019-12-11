/**
 * [RSA加密，http://www.ohdave.com]
 *
 * 十六进制的密钥
 * 
 * @Author   Lonny
 * @Email    lonnypeng@baogongpo.com
 * @DateTime 2019-12-11
 * @param    {[type]}                exports) {               var $ [description]
 * @return   {[type]}                         [description]
 */
layui.define(['jquery'], function (exports) {
    var $ = layui.jquery,
        ohdaveUrl = ["BigInt", "Barrett", "RSA"];

    $(ohdaveUrl).each(function (key, value) {
        loadScript("http://www.ohdave.com/rsa/" + value + ".js");
    });

    function loadScript(url) {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.async = 'async';
        script.src = url;

        document.body.appendChild(script);
    }

    var reloadOhdaveTime = setInterval(function () {
        if(typeof setMaxDigits == 'function' 
            && typeof RSAKeyPair != 'undefined' 
            && typeof encryptedString != 'undefined') {

            clearInterval(reloadOhdaveTime);

            var obj = (function ($) {
                'use strict';

                setMaxDigits(130);
                var key = new RSAKeyPair("10001", "", "C26B6793DC3B8FBE48B5FD62B2A8E7522021363D404951652DDB75862EA5AA555972163B129ECDEC4E821BFD93C8DCBBDBB6C69A53813C4FCAB724EAE5931D4BE80DC921E6DCE41BB7F50CAEECA1AAF76982F9BF9A6FC27B4EA9A6552C575254069786E054E6533910262F2349BBC669223017547B2BDD79BAEE3EAD16FB7113");

                /**
                 * [encryptByPublicKey 公钥加密]
                 * @Author   Lonny
                 * @Email    lonnypeng@baogongpo.com
                 * @DateTime 2019-12-11
                 * @param    {[type]}                string [description]
                 * @return   {[type]}                       [description]
                 */
                function encryptByPublicKey(string) {
                    return encryptedString(key, string.toString());
                }

                return {
                    encryptByPublicKey: function (string) {return encryptByPublicKey(string);}
                };
            }(this));

            exports('rsa', obj);
        }
    }, 100);
});