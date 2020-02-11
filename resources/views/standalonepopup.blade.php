<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <title>elFinder 2.0</title>

        <!-- jQuery and jQuery UI (REQUIRED) -->
        <link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/smoothness/jquery-ui.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
        <link href="/plugins/bower_components/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
        <script src="/plugins/bower_components/sweetalert/sweetalert.min.js"></script>
        <script src="/plugins/bower_components/sweetalert/jquery.sweet-alert.custom.js"></script>
        <!-- elFinder CSS (REQUIRED) -->
        <link rel="stylesheet" type="text/css" href="{{ asset($dir . '/css/elfinder.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset($dir . '/css/theme.css') }}">

        <!-- elFinder JS (REQUIRED) -->
        <script src="{{ asset($dir . '/js/elfinder.min.js') }}"></script>

        @if($locale)
            <!-- elFinder translation (OPTIONAL) -->
            <script src="{{ asset($dir . "/js/i18n/elfinder.$locale.js") }}"></script>
        @endif
        <!-- Include jQuery, jQuery UI, elFinder (REQUIRED) -->

        <script type="text/javascript">
            $().ready(function () {
                var elf = $('#elfinder').elfinder({
                    // set your elFinder options here
                    @if($locale)
                        lang: '{{ $locale }}', // locale
                    @endif
                    customData: {
                        _token: '{{ csrf_token() }}'
                    },
                    url: '{{ route("elfinder.connector") }}',  // connector URL
                    soundPath: '{{ asset($dir.'/sounds') }}',
                    dialog: {width: 900, modal: true, title: 'Select a file'},
                    resizable: false,
                    commandsOptions: {
                        getfile: {
                            multiple: true,
                            oncomplete: 'destroy'
                        }
                    },
                    getFileCallback: function (file) {
                        var urls = $.map(file, function(f) { return f; });
                        var max_files = '{{ $max_files }}';
                        var type = '@json($type)';
                        var route = '{{ $route }}';
                        var img_types = '@json($imgs)';

                            if (urls.length > max_files) {
                             window.parent.alertWarning(max_files);
                            }else if(urls.length){
                            if (window.parent.CheckCount(max_files,urls.length)){
                                for (var a of urls){
                                    if (!type.includes(a.mime)){
                                        window.parent.validateFiles(img_types);
                                        break;
                                        }else {
                                        window.parent.processSelectedFile(a,route);
                                            }
                                        }
                                } else {
                                window.parent.CheckCount(max_files,urls.length);
                                                      }
                            }
                        parent.jQuery.colorbox.close();
                    }
                }).elfinder('instance');
            });
        </script>

    </head>
    <body>

        <!-- Element where elFinder will be created (REQUIRED) -->
        <div id="elfinder"></div>

    </body>
</html>
