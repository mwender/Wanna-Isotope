module.exports = function(grunt){
    require('load-grunt-tasks')(grunt); // npm install --save-dev load-grunt-tasks

    grunt.initConfig({
        less: {
          development: {
            options: {
              compress: false,
              yuicompress: false,
              optimization: 2,
              relativeUrls: true
            },
            files: {
              // target.css file: source.less file
              'public/css/main.css': 'public/less/main.less'
            }
          },
          production: {
            options: {
              compress: true,
              yuicompress: true,
              optimization: 2,
              relativeUrls: true
            },
            files: {
              'public/css/main.css': 'public/less/main.less'
            }
          }
        },
        watch: {
          options: {
            livereload: true,
          },
          styles: {
            files: ['public/less/**/*.less'], // which files to watch
            tasks: ['less:development'],
            options: {
              nospawn: true
            }
          }
        }


    });
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('build', ['less:production']);
    grunt.registerTask('builddev', ['less:development']);
}