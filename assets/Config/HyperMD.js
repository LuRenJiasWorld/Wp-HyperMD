/**
 * 增强组件：https://laobubu.net/HyperMD/#./docs/options-for-addons.md
 * 第三方组件：https://laobubu.net/HyperMD/#./docs/powerpacks.md
 */

(function (doc, win, wphypermd, wp) {
    /* 获取用户数据，配置编辑器环境 */
    //emoji表情类型
    var isEmojiPackages;
    var isEmojiPowerpack;
    wphypermd.isEmoji === 'on' ? isEmojiPackages = {name: 'twemoji', main: '2/twemoji.amd.js'} : isEmojiPackages = '';
    wphypermd.isEmoji === 'on' ? isEmojiPowerpack = 'hypermd/powerpack/fold-emoji-with-twemoji' : isEmojiPowerpack = '';

    //科学公式类型
    var mathTypePackages;
    var mathTypePowerpack;
    switch (wphypermd.mathType) {
        case 'disable' :
            mathTypePackages = '';
            mathTypePowerpack = '';
            break;
        case 'katex' :
            mathTypePackages = {name: 'katex', main: 'dist/katex.min.js'};
            mathTypePowerpack = 'hypermd/powerpack/fold-math-with-katex';
            break;
        case 'mathjax' :
            mathTypePackages = {name: 'mathjax', main: 'MathJax.js'};
            mathTypePowerpack = 'hypermd/powerpack/fold-math-with-mathjax';
            break;
        default :
            mathTypePackages = '';
            mathTypePowerpack = '';
    }

    //1. 配置 RequireJS
    requirejs.config({
        baseUrl: '//cdn.jsdelivr.net/npm/',
        //baseUrl: wphypermd.hypermdURL + '/assets/',
        packages: [
            {name: 'hypermd', main: 'everything.js'},
            {name: 'codemirror', main: 'lib/codemirror.js'},
            {name: 'marked', main: 'lib/marked.js'},
            {name: 'turndown', main: 'lib/turndown.browser.umd.js'},
            {name: 'turndown-plugin-gfm', main: 'dist/turndown-plugin-gfm.js'},
            mathTypePackages,
            isEmojiPackages
        ],
        waitSeconds: 30
    });

    //2. 声明主要模块
    require([
        /**
         * 核心加载
         */
        'codemirror/lib/codemirror',
        'hypermd/core',

        /**
         * HyperMD模块
         */
        'hypermd/mode/hypermd',
        'hypermd/addon/click',
        'hypermd/addon/cursor-debounce',
        'hypermd/addon/fold',
        'hypermd/addon/fold-code',
        'hypermd/addon/fold-html',
        'hypermd/addon/hide-token',
        'hypermd/addon/hover',
        'hypermd/addon/insert-file',
        'hypermd/addon/mode-loader',
        'hypermd/addon/paste',
        'hypermd/addon/read-link',
        'hypermd/addon/skeleton',
        'hypermd/addon/table-align',
        wphypermd.isEmoji === 'on' ? 'hypermd/addon/fold-emoji' : '',
        wphypermd.mathType !== 'disable' ? 'hypermd/addon/fold-math' : '',

        'hypermd/keymap/hypermd',

        /**
         * 使用 PowerPack 和各种第三方库来增强 HyperMD 功能
         * 文档：http://laobubu.net/HyperMD/?directOpen#./docs/powerpacks.md
         */
        //turndown插件 - 添加删除线和表格支持
        'turndown-plugin-gfm',
        //当鼠标悬停在链接或脚注ref上时，显示相关的脚注
        'hypermd/powerpack/hover-with-marked',
        //复制富文本粘贴转换成markdown
        'hypermd/powerpack/paste-with-turndown',
        //mermaid：https://laobubu.net/HyperMD/docs/examples/mermaid.html
        wphypermd.isMermaid === 'on' ? 'hypermd/powerpack/fold-code-with-mermaid' : '',
        //上传到sm图库
        wphypermd.imagePaste === 'smms' ? 'hypermd/powerpack/insert-file-with-smms' : '',
        mathTypePowerpack,
        isEmojiPowerpack

    ], function (CodeMirror, HyperMD) {
        'use strict';
        // WordPress编辑器TextArea对象
        var wpContent = doc.getElementById('content');
        // 注入HyperMD编辑器
        var wpeditor = HyperMD.fromTextArea(wpContent, {
            /* 这里可以设置一些编辑器插件选项：http://laobubu.net/HyperMD/?directOpen#./docs/options-for-addons.md */

            /* 编辑器配置模式：https://laobubu.net/HyperMD/?directOpen#./docs/options-for-mode.md */
            mode: {
                name: 'hypermd', // 编辑器加载模式 'hypermd'或者'codemirror'
                strikethrough: true, // 删除线语法 ~~xx~~
                taskLists: true, // 任务管理 [] xx / [x] xx
                front_matter: false, // 内容块变量 WordPress不需要该功能
                orgModeMarkup: false, // 标记模式 同上
                hashtag: false, // 标签语法 同上

                math: wphypermd.mathType !== 'disable',  // 数学公式
                table: true, // 表格
                toc: true, // 文章目录
                emoji: wphypermd.isEmoji === 'on'  // Emoji表情

            },
            /* 当鼠标悬停在链接或脚注参考上时，会显示相关的脚注 */
            hmdHover: wphypermd.isHover === 'on',
            /* 点击打开 链接/跳转脚注/切换TODO */
            hmdClick: wphypermd.isClick === 'on',
            /* 在粘贴之前将粘贴板的内容转换为Markdown */
            hmdPaste: wphypermd.isPaste === 'on',
            /* markdown解析真实内容 */
            hmdFold: {
                image: true, //图像
                link: true, //链接
                math: wphypermd.mathType !== 'disable', //数学
                html: true, //富文本
                emoji: wphypermd.isEmoji === 'on', //emoji
                code: true //代码块
            },
            /* 解析特殊代码块 */
            hmdFoldCode: {
                mermaid: true
            },
            /* 自动加载代码高亮模式 */
            hmdModeLoader: '~codemirror/',
            /* 自动显示/隐藏markdown标记，如`##`或`*` */
            hmdHideToken: wphypermd.isHideToken === 'on',
            /* 表格对齐 */
            hmdTableAlign: wphypermd.isTableAlign === 'on'
        });

        //重设高度
        wpeditor.setSize(null, "640px");
        //聚焦编辑器
        wpeditor.focus();

        //图片粘贴 - 选择本地媒体上传图片业务处理
        if (wphypermd.imagePaste === 'local') {
            wpeditor.setOption('hmdInsertFile', {
                byPaste: true, //粘贴上传
                byDrop: true, //拖拽上传
                fileHandler: function (files, action) {
                    var spinGIF = 'data:image/gif;base64,R0lGODlhEAAQAMMMAAQEBIGBgby8vEJCQtzc3GJiYqCgoC8vL8zMzFRUVBkZGY+Pj+rq6nJycq2trQAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQJDAAPACwAAAAAEAAQAAAEVvDJSat9rDV2JSOPcRzGg4AUUwxnkjACcKASMQwCFShATiG+CSERSBE4HYohsegQEEhDoXl5ohjQpOVpYSAnjECD9iA4gpNFACTIwRBJxmLx1Z60eEkEACH5BAkIAA8ALAEAAQAOAA4AAARQ8EnJQmAzM/LEOMJDYFRTEITFCMdAEkUSTotyIBMyT4wzb6SMBLFAOBoG4eQAGAiQSgkzAYyKFpzHRhmUGLAaBG7iWGDEWkRWaXBYM4RxJgIAIfkECQwADwAsAQABAA4ADgAABE/wScnWYjNPkZJ4BDYtQWgxyJCITFAQmXEMsEQg2iPgtpgjC4Qg8Mk9BooCsJhDNkBGm6HG8NkSgYxjmmkAAEyEA0OAOQCKmkYoEag15VwEACH5BAkIAA8ALAEAAQAOAA4AAARO8EnJjGMzT9IaeQQ2OcZHPkjRiI+xfJOQFCwBZwKi7RTCEI6bpjEIAHW8xmHByzB8ExbFgZQgoBOD4nAj+BCHA0IQFkoCAAAzxCMkEuYIACH5BAkMAA8ALAEAAQAOAA4AAARP8MmJ0LyXhcWwFEIHPsTWSY5BXEjTnA+zYsjsYTLDCDa2DCre7RFIGIYTBuJU7D0Elg8A0Lg4DoMZQQFQDQYIwSABI1gWCsWRALsQCg1nBAAh+QQJCAAPACwBAAEADgAOAAAETPDJSci82BlMkUQeYTgXyFzEsl0nVn2LwEkMwQzAMT9G4+C6WU/AWFhmtRbC0ZoIjg/CQbGSCBKFlvRADAQYiEKjWXsIDgOZDeltSiIAIfkECQwADwAsAQABAA4ADgAABE7wyUnIvI8gKTbOCuA8jMU43iMAQHMRzjg1ifUyErKkWPkUisGHExAAE0PVjmCwDZ0IwQfhJAwGslyjgSNdBYzFotRYXHyCREKaJIm7kwgAIfkEBQgADwAsAQABAA4ADgAABE3wSUlKITMzHABYmcQMh0AMA4ZgEnIo4MQgSCY4TJiLyB5mgUHj13IQgkMiwTjzEScEwY/AelQSUujCIGsUeg4Dg7FwaDCERqP6NJhDEQA7';
                    var errorPNG = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAP1BMVEWIioX29/bu7uz+//79/f3S0tL09PPy8vHw8O74+fjMAADU2ND6+vr39/bY3NW9vrzp6ubl5+Pi5N/e4duytLE6MtxfAAAAbUlEQVQY022PSRLDMAgEgZBhMXLW/781cqKDYrtvdPWBoRvNbOflD+qCZ7rQdX09H3dxcClvxeKQgX2LRSRFPGdhaRnmgiHcIgMw559wQ0Y2hrUheh+9YdQQaFFa1aCnf+jh0+sE77co0Xs3/wNPXARclYchfgAAAABJRU5ErkJggg==';
                    var supportBlobURL = typeof URL !== 'undefined';
                    var placeholderUploadingClass = "hmd-file-uploading";
                    var placeholderUploadedClass = "hmd-file-uploaded";
                    var unfinishedCount = 0;
                    var placeholderForAll = document.createElement("span");
                    placeholderForAll.className = "smms-hosted-items";
                    action.setPlaceholder(placeholderForAll);
                    var uploads = [];
                    for (var i = 0; i < files.length; i++) {
                        var file = files[i];
                        if (!/image\//.test(file.type))
                            continue;
                        var blobURL = supportBlobURL ? URL.createObjectURL(file) : spinGIF;
                        var name_1 = file.name.match(/[^\\\/]+\.\w+$/)[0];
                        var placeholder = document.createElement("img");
                        placeholder.onload = resize; // img size changed
                        placeholder.className = placeholderUploadingClass;
                        placeholder.src = blobURL;
                        placeholderForAll.appendChild(placeholder);
                        var task = {
                            blobURL: blobURL,
                            name: name_1,
                            url: null,
                            placeholder: placeholder
                        };
                        uploads.push(task);
                        unfinishedCount++;
                        var reader = new FileReader();
                        reader.readAsDataURL(file);
                        reader.onload = function (e) {
                            var fileBase64 = e.target.result;
                            Upload_One(fileBase64, uploadCallback.bind(null, task));
                        };
                    }
                    return uploads.length > 0;

                    /**
                     * 上传到WP服务器
                     * @param file
                     * @param callback
                     */
                    function Upload_One(file, callback) {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'imagepaste_action',
                                dataurl: file
                            },
                            success: function (request) {
                                var obj = JSON.parse(request);
                                callback(obj.url);
                            }
                        });
                    }

                    /**
                     * 调整
                     */
                    function resize() {
                        action.resize();
                    }

                    /**
                     * 上传图片成功url载入
                     * @param task
                     * @param url
                     */
                    function uploadCallback(task, url) {
                        task.url = url || "error";
                        var placeholder = task.placeholder;
                        placeholder.className = placeholderUploadedClass;
                        var _preloadDone = preloadCallback.bind(null, task);
                        if (url) {
                            var img = document.createElement("img");
                            img.addEventListener("load", _preloadDone, false);
                            img.addEventListener("error", _preloadDone, false);
                            img.src = url;
                        }
                        else {
                            placeholder.src = errorPNG;
                            _preloadDone();
                        }
                    }

                    /**
                     * 插入markdown图片地址
                     * @param task
                     */
                    function preloadCallback(task) {
                        if (supportBlobURL)
                            URL.revokeObjectURL(task.blobURL);
                        if (--unfinishedCount === 0) {
                            var texts = uploads.map(function (it) {
                                return "![" + it.name + "](" + it.url + ")";
                            });
                            action.finish(texts.join(" ") + " ");
                        }
                    }
                }
            });
        }

        //实时监听编辑器内容，将内容更新到wpContent的value值
        //修复F5丢失数据的问题
        wpeditor.on('change', function (editor) {
            wpContent.value = editor.getValue();
        });

        // WP Media module支持
        var original_wp_media_editor_insert = wp.media.editor.insert;
        wp.media.editor.insert = function (html) {
            //console.log(html);
            //创建新的DOM
            var htmlDom = doc.createElement('div');
            htmlDom.style.display = 'none';
            htmlDom.id = 'htmlDom';
            htmlDom.innerHTML = html;
            doc.body.appendChild(htmlDom);
            var dom = doc.getElementById('htmlDom').childNodes[0];
            var markdownSrc;
            //console.log(dom.localName);
            switch (dom.localName) {
                case 'a':
                    if (dom.childNodes[0].localName === 'img') {
                        markdownSrc = '[![](' + dom.childNodes[0].src + ')](' + dom.href + ')';
                    } else {
                        markdownSrc = '[' + dom.innerText + '](' + dom.href + ')';
                    }
                    break;
                case 'img':
                    var htmlSrc = doc.getElementsByClassName('alignnone')[0].src;
                    var htmlAlt = doc.getElementsByClassName('alignnone')[0].alt;
                    markdownSrc = '![' + htmlAlt + '](' + htmlSrc + ')';
                    break;
                default:
                    markdownSrc = doc.getElementById('htmlDom').innerHTML;
            }
            original_wp_media_editor_insert(markdownSrc);
            insertCodeMirror(markdownSrc);
            //移除dom
            doc.getElementById('htmlDom').remove();
        };

        /**
         * 在光标之处添加内容
         * @param data 数据
         */
        function insertCodeMirror(data) {
            var doc = wpeditor.getDoc();
            var cursor = doc.getCursor(); // 获取光标位置的行号
            var line = doc.getLine(cursor.line); // 获取行内容
            var pos = { // 创建一个新对象以避免原始选择的变化
                line: cursor.line,
                ch: line.length - 1 // 将字符位置设置为该行的结尾
            };
            doc.replaceRange(data, pos); // 新增内容
        }
    });
})(document, window, window.WPHyperMD, window.wp);