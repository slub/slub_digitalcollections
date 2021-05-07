
module.exports = function(grunt) {
    require('jit-grunt')(grunt);

    grunt.initConfig({
        less: {
            development: {
                options: {
                    sourceMap: true,
                    compress: true,
                    yuicompress: true,
                    optimization: 2
                },
                files: {
                    "Resources/Public/Css/Digitalcollections.css" : "Resources/Private/Less/Digitalcollections.less",
                    "Resources/Public/Css/DigitalcollectionsLists.css" : "Resources/Private/Less/DigitalcollectionsLists.less",
                    "Resources/Public/Css/BaseDesign.css" : "Resources/Private/Less/BaseDesign.less",
                }
            }
        },
        terser: {
            development: {
                options: {
                    compress: true,
                    output: {
                        comments: false
                    }
                },
                files: {
                        "Resources/Public/JavaScript/DigitalcollectionsScripts.js" : [
                            'Resources/Private/JavaScript/modernizrCustom.js',
                            'Resources/Private/JavaScript/Cookies.js',
                            'Resources/Private/JavaScript/DigitalcollectionsScripts.js',
                        ],
                        "Resources/Public/JavaScript/DigitalcollectionsListScripts.js" : [
                            'Resources/Private/JavaScript/modernizrCustom.js',
                            'Resources/Public/JavaScript/Highlight/colcade.js',
                            'Resources/Private/JavaScript/DigitalcollectionsListScripts.js',
                        ],
                }
            }
        },
        watch: {
            styles: {
                files: ['Resources/Private/Less/**/*.less'],
                tasks: ['less'],
                options: {
                    spawn: false
                }
            },
            js: {
                files: ['Resources/Private/JavaScript/*.js'],
                tasks: ['terser'],
                options: {
                    spawn: false
                }
            },
        }
    });
    grunt.file.setBase('../')
    grunt.registerTask('default', ['terser','less','watch']);
};
