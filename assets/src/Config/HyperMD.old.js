(function (doc,win,hypermd,wp) {
    // 1. 配置 RequireJS
    requirejs.config({
        baseUrl: '//cdn.jsdelivr.net/npm/',
        // (如果你使用 CDN 遇到问题，删除这段)
        // RequireJS doesn't read package.json or detect entry file.
        packages: [
            { name: 'codemirror', main: 'lib/codemirror.js' },
            //{ name: 'mathjax', main: 'MathJax.js' },
            //{ name: 'katex', main: 'dist/katex.min.js' },
            { name: 'marked', main: 'lib/marked.js' },
            { name: 'turndown', main: 'lib/turndown.browser.umd.js' },
            { name: 'turndown-plugin-gfm', main: 'dist/turndown-plugin-gfm.js' }
        ],
        waitSeconds: 15
    });

    // 2. Declare your main module
    require([
        'codemirror/lib/codemirror',
        'hypermd/everything',  // 如果想选择性地只载入部分组件, 参考 demo/index.js

        // 如果需要为特殊元素添加语法高亮，请载入对应的模式
        "codemirror/mode/htmlmixed/htmlmixed", // Markdown 内嵌HTML
        "codemirror/mode/stex/stex", // TeX 数学公式
        "codemirror/mode/yaml/yaml", // Front Matter

        // 随后，使用 PowerPack 和各种第三方库来增强 HyperMD 功能。
        // 具体可用列表请参考文档，或者 demo/index.js
        //'hypermd/powerpack/fold-math-with-katex',

        'hypermd/powerpack/paste-with-turndown',
        'turndown-plugin-gfm'

    ], function (CodeMirror, HyperMD) {
        // WordPress编辑器TextArea对象
        var wpContent = doc.getElementById('content');
        // 注入HyperMD编辑器
        var editor = HyperMD.fromTextArea(wpContent, {
            /* 这里可以设置一些 编辑器选项 */
            height: "800px",
            mode: {
                name: "hypermd",
                /* mode options goes here*/
                hashtag: false
            }
        });

        // 重设高度
        editor.setSize(null, 800);

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
        function insertCodeMirror(data){
            var doc = editor.getDoc();
            var cursor = doc.getCursor(); // 获取光标位置的行号
            var line = doc.getLine(cursor.line); // 获取行内容
            var pos = { // 创建一个新对象以避免原始选择的变化
                line: cursor.line,
                ch: line.length - 1 // 将字符位置设置为该行的结尾
            };
            doc.replaceRange(data, pos); // 新增内容
        }
    });
})(document,window,window.WPHyperMD,window.wp);