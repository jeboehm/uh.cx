module.exports = function(grunt) {
  grunt.initConfig({

    // Configuration
    paths: {
      assets: {
        css: './app/Resources/assets/less/',
        js: './app/Resources/assets/js/',
        vendor: './app/Resources/assets/vendor/'
      },
      css: './web/css/',
      js: './web/js/',
      fonts: './web/fonts/'
    },

    // Concat required javascripts to frontend.js
    concat: {
      options: {
        separator: ';'
      },
      frontend: {
        src: [
          '<%= paths.assets.vendor %>jquery/dist/jquery.js',
          '<%= paths.assets.vendor %>bootstrap/dist/js/bootstrap.js',
          '<%= paths.assets.vendor %>clipboard/dist/clipboard.js',
          '<%= paths.assets.vendor %>cookieconsent/build/cookieconsent.min.js',
          '<%= paths.assets.js %>bookmarklet.js',
          '<%= paths.assets.js %>info.js'
        ],
        dest: '<%= paths.js %>frontend.js'
      }
    },

    // Compress javascript frontend.js -> frontend.min.js
    uglify: {
      options: {
        mangle: false
      },
      frontend: {
        files: {
          '<%= paths.js %>frontend.min.js': '<%= paths.js %>frontend.js'
        }
      }
    },

    // Compile less files to frontend.min.css
    less: {
      frontend: {
        options: {
          compress: true
        },
        files: {
          "<%= paths.css %>frontend.min.css": "<%= paths.assets.css %>frontend.less"
        }
      }
    },

    // Copy bootstrap fonts
    copy: {
      bootstrap_fonts: {
        files: [
          {
            expand: true,
            cwd: '<%= paths.assets.vendor %>bootstrap/dist/fonts/',
            src: [ '**' ],
            dest: '<%= paths.fonts %>',
            filter: 'isFile'
          }
        ]
      }
    },

    // Configure watcher for development
    watch: {
      frontend: {
        files: [ '<%= paths.assets.js %>*.js' ],
        tasks: [ 'concat', 'uglify' ]
      },
      less: {
        files: [ '<%= paths.assets.css %>*.less' ],
        tasks: [ 'less' ]
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.registerTask('default', [ 'concat', 'uglify', 'less', 'copy' ]);
};
