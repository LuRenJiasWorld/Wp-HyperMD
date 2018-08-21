var is_running_demo = /\.github\.|laobubu\.net/.test(location.hostname)

var demo_page_baseurl = window.location.href.replace(/[\?\#].*$/, '').replace(/\/[^\/]+$/, '/')
var demo_page_lib_baseurl = is_running_demo ? "https://cdn.jsdelivr.net/npm/" : (demo_page_baseurl + "node_modules/")
var demo_filename = "README.md"

if (requirejs) requirejs.config({
    // baseUrl: "node_modules/",                   // using local version
    // baseUrl: "https://cdn.jsdelivr.net/npm/",   // or use CDN
    baseUrl: demo_page_lib_baseurl,

    paths: {
        // HyperMD is not from node_modules nor CDN:
        // "hypermd": "./",
        "hypermd": demo_page_baseurl + ".",
    },

    // Remove this line if you occur errors with CDN
    packages: requirejs_packages, // see: requirejs_packages.js

    waitSeconds: 15
})

require([
    /**
     * 核心加载
     */
    'codemirror/lib/codemirror',
    'hypermd/core',

    ///////////////////////////////////////
    /// CodeMirror                      ///
    ///////////////////////////////////////

    // 代码高亮
    "codemirror/mode/htmlmixed/htmlmixed", // for embedded HTML
    "codemirror/mode/stex/stex", // for Math TeX Formular
    "codemirror/mode/yaml/yaml", // for Front Matters

    // NOTE: For code blocks,
    //       addon "mode-loader" can load modes automatically if configured properly
    'codemirror/mode/javascript/javascript',  // eg. javascript

    /**
     * HyperMD模块
     */

    'hypermd/mode/hypermd', // 必要

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

    'hypermd/keymap/hypermd',

    /**
     * 使用 PowerPack 和各种第三方库来增强 HyperMD 功能
     * 具体可用列表请参考文档，或者 demo/index.js
     */
    'hypermd/powerpack/fold-code-with-flowchart',
    'hypermd/powerpack/fold-code-with-mermaid',
    'hypermd/powerpack/fold-emoji-with-emojione',
    'hypermd/powerpack/fold-emoji-with-twemoji',
    'hypermd/powerpack/fold-math-with-katex',
    'hypermd/powerpack/fold-math-with-mathjax',
    'hypermd/powerpack/hover-with-marked',
    'hypermd/powerpack/insert-file-with-smms',
    'hypermd/powerpack/paste-with-turndown',
    'turndown-plugin-gfm',

], function (CodeMirror, HyperMD) {
    'use strict';
    var myTextarea = document.getElementById('demo')

    // HyperMD magic. See https://laobubu.net/HyperMD/docs/
    var editor = HyperMD.fromTextArea(myTextarea, {
        mode: {
            name: "hypermd",
            hashtag: true,  // this syntax is not actived by default
        },

        hmdClick: clickHandler,
        hmdFold: {
            image: true,
            link: true,
            math: true,
            html: true, // maybe dangerous
            emoji: true,
        }
    })
    editor.setSize(null, "100%") // set height

    // for debugging
    window.CodeMirror = CodeMirror
    window.HyperMD = HyperMD
    window.editor = editor
    window.cm = editor

    // for demo page only:
    document.body.className += " loaded"
    document.getElementById('loadSplash').setAttribute('style', 'display:none')

    load_and_update_editor(demo_filename)

    // Preview Tex Math formula
    // @see demo/math-preview.js
    init_math_preview(editor)

    // Watch editor and generate TOC
    // @see demo/toc.js
    init_toc(editor)

    // @see demo/lab.js
    init_lab(editor)
}, function (err) {
    var div = document.getElementById('loadErrorSplash')
    var ul = document.getElementById('loadErrorList')

    div.style.display = ''
    var mods = err.requireModules
    for (var i = 0; i < mods.length; i++) {
        var li = document.createElement("li")
        li.textContent = mods[i]
        ul.appendChild(li)
    }
})

var demoPageConfig = {
    directOpen: /directOpen/.test(window.location.search),
    mathPreview: true,
}

function clickHandler(info, cm) {
    if (info.type === "link" || info.type === "url") {
        var url = info.url
        if ((demoPageConfig.directOpen || info.ctrlKey || info.altKey) && !/^http/i.test(url) && /\.(?:md|markdown)$/.test(url.replace(/[?#].*$/, ''))) {
            // open a link whose URL is *.md with ajax_load_file
            // and supress HyperMD default behavoir
            load_and_update_editor(url) // see index2.js
            return false
        } else if (demoPageConfig.directOpen && url) {
            window.open(url)
            return false
        } else if (/^\[(?:Try out|试试看)\]$/i.test(info.text)) {
            demo_tryout(info) // see index2.js
            return false
        }
    }
    if (info.type === "hashtag") {
        var msg = "You clicked a hashtag"
        if (info.ctrlKey) msg += " (with Ctrl)"
        if (info.altKey) msg += " (with Alt)"
        console.log(msg, info.text, info.pos)
    }
}
