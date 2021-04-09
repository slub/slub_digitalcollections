
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
                        "Resources/Public/Javascript/DigitalcollectionsScripts.js" : [
                            'Resources/Private/Javascript/modernizrCustom.js',
                            'Resources/Private/Javascript/Cookies.js',
                            'Resources/Private/Javascript/DigitalcollectionsScripts.js',
                        ],
                        "Resources/Public/Javascript/DigitalcollectionsListScripts.js" : [
                            'Resources/Private/Javascript/modernizrCustom.js',
                            'Resources/Public/Javascript/Highlight/colcade.js',
                            'Resources/Private/Javascript/DigitalcollectionsListScripts.js',
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
                files: ['Resources/Private/Javascript/*.js'],
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
