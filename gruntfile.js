
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
                }
            }
        },
        uglify: {
            development: {
                options: {
                    compress: true,
                    preserveComments: false,
                    yuicompress: true,
                    optimization: 2
                },
                files: {
                    "Resources/Public/Javascript/DigitalcollectionsScripts.js" : ['Resources/Private/Javascript/modernizrCustom.js', 'Resources/Private/Javascript/Cookies.js', 'Resources/Private/Javascript/DigitalcollectionsScripts.js'],
                    "Resources/Public/Javascript/DigitalcollectionsListScripts.js" : ['Resources/Private/Javascript/modernizrCustom.js', 'Resources/Private/Javascript/colcade.js', 'Resources/Private/Javascript/DigitalcollectionsListScripts.js']
                }
            }
        },
        watch: {
            styles: {
                files: ['Resources/Private/Less/**/*.less'],
                tasks: ['less'],
                options: {
                    nospawn: true
                }
            },
            js: {
                files: ['Resources/Private/Javascript/*.js'],
                tasks: ['uglify']
            }
        }
    });
    grunt.registerTask('default', ['less','uglify','watch']);
};
