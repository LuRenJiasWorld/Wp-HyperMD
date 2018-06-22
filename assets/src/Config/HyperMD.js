(function (doc,win,hypermd) {
    // 1. 配置 RequireJS
    requirejs.config({
        // baseUrl: "/node_modules/",                  // 使用本地保存的库
        // baseUrl: "https://cdn.jsdelivr.net/npm/",   // 使用CDN加载库
        baseUrl: hypermd.editormdUrl + '/assets/',
        //baseUrl: "https://cdn.jsdelivr.net/npm/",

        // (如果你使用 CDN 遇到问题，删除这段)
        // RequireJS doesn't read package.json or detect entry file.
        packages: [
            { name: 'codemirror', main: 'lib/codemirror.js' },
            { name: 'mathjax', main: 'MathJax.js' },
            { name: 'katex', main: 'dist/katex.min.js' },
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
        'hypermd/powerpack/fold-math-with-katex',

        'hypermd/powerpack/paste-with-turndown',
        'turndown-plugin-gfm'

    ], function (CodeMirror, HyperMD) {
        var myTextarea = document.getElementById('content')
        var editor = HyperMD.fromTextArea(myTextarea, {
            /* 这里可以设置一些 编辑器选项 */
        })
    })
})(document,window,window.HyperMD)