(function (doc, win, wphypermd, wp) {
    //1. 配置 RequireJS
    requirejs.config({
        baseUrl: '//cdn.jsdelivr.net/npm/',
        //baseUrl: wphypermd.hypermdURL + '/assets/',
        // paths: {
        //     //本地资源
        //     "hypermd": '//cdn.jsdelivr.net/npm/'
        // },
        packages: [
            {name: 'hypermd', main: 'everything.js'},
            {name: 'codemirror', main: 'lib/codemirror.js'},
            {name: 'mathjax', main: 'MathJax.js'},
            {name: 'katex', main: 'dist/katex.min.js'},
            {name: 'marked', main: 'lib/marked.js'},
            {name: 'turndown', main: 'lib/turndown.browser.umd.js'},
            {name: 'turndown-plugin-gfm', main: 'dist/turndown-plugin-gfm.js'},
            {name: 'emojione', main: 'lib/js/emojione.min.js'},
            {name: 'twemoji', main: '2/twemoji.amd.js'},
            {name: 'flowchart.js', main: 'release/flowchart.min.js'},
            {name: 'Raphael', main: 'raphael.min.js'}, // stupid
            {name: 'raphael', main: 'raphael.min.js'}
            //{name: 'mermaid', main: 'dist/mermaid.js'} // AMD无法载入，需要手动加载mermaid
        ],
        waitSeconds: 15
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
        'hypermd/mode/hypermd', //必要，不可删除

        'hypermd/addon/click',
        'hypermd/addon/cursor-debounce',
        'hypermd/addon/fold',
        'hypermd/addon/fold-code',
        'hypermd/addon/fold-emoji',
        'hypermd/addon/fold-html',
        'hypermd/addon/fold-math',
        'hypermd/addon/hide-token',
        'hypermd/addon/hover',
        'hypermd/addon/insert-file',
        'hypermd/addon/mode-loader',
        'hypermd/addon/paste',
        'hypermd/addon/read-link',
        'hypermd/addon/skeleton',
        'hypermd/addon/table-align',
        //键盘布局
        'hypermd/keymap/hypermd',

        /**
         * 使用 PowerPack 和各种第三方库来增强 HyperMD 功能
         * 文档：http://laobubu.net/HyperMD/?directOpen#./docs/powerpacks.md
         */
        //turndown插件 - 添加删除线和表格支持
        'turndown-plugin-gfm',
        //流程图：https://laobubu.net/HyperMD/docs/examples/flowchart.html
        // 'hypermd/powerpack/fold-code-with-flowchart',
        //mermaid：https://laobubu.net/HyperMD/docs/examples/mermaid.html
         'hypermd/powerpack/fold-code-with-mermaid',
        //emojione表情
        'hypermd/powerpack/fold-emoji-with-emojione',
        //twitter emoji表情
        'hypermd/powerpack/fold-emoji-with-twemoji',
        //KaTeX科学公式
        'hypermd/powerpack/fold-math-with-katex',
        //mathjax科学公式
        //'hypermd/powerpack/fold-math-with-mathjax',
        //当鼠标悬停在链接或脚注ref上时，显示相关的脚注
        'hypermd/powerpack/hover-with-marked',
        //上传到sm图库
        'hypermd/powerpack/insert-file-with-smms',
        //复制富文本粘贴转换成markdown
        'hypermd/powerpack/paste-with-turndown'

    ], function (CodeMirror, HyperMD) {
        'use strict';
        // WordPress编辑器TextArea对象
        var wpContent = doc.getElementById('content');
        // 注入HyperMD编辑器
        var wpeditor = HyperMD.fromTextArea(wpContent, {
            /* 这里可以设置一些编辑器插件选项：http://laobubu.net/HyperMD/?directOpen#./docs/options-for-addons.md */
            height: "640px", // 编辑器高度
            /* 编辑器配置模式：https://laobubu.net/HyperMD/?directOpen#./docs/options-for-mode.md */
            mode: {
                name: 'hypermd', // 编辑器加载模式 'hypermd'或者'codemirror'
                strikethrough: true, // 删除线语法 ~~xx~~
                taskLists: true, // 任务管理 [] xx / [x] xx
                front_matter: false, // 内容块变量 WordPress不需要该功能
                orgModeMarkup: false, // 标记模式 同上
                hashtag: false, // 标签语法 同上

                highlightFormatting: false, // 单独突出显示markdown元数据字符例如：*[]()
                math: false,  // 数学公式
                table: true, // 表格
                toc: false, // 文章目录
                emoji: false, // Emoji表情

            },
            hmdModeLoader: '~codemirror/', // 自动加载代码突出显示模式
            /* 自动显示/隐藏markdown标记，如`##`或`*` */
            hmdHideToken: {
                enabled: false,
                line: true,
                tokenTypes: "em|strong|strikethrough|code|link|task".split("|"),
            },
            hmdFold: {
                image: true,
                link: true,
                math: true,
                html: false
            }
        });

        // 重设高度
        wpeditor.setSize(null, "640px");
        // 聚焦编辑器
        //wpeditor.focus();

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