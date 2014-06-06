<html style="overflow:hidden; margin:0; padding:0; border:none;">
    <head></head>
    <body>
        <div id="video_<?php echo $response; ?>" class="videoContainer"></div>
        <script type="text/javascript">

            var videoPlayerID = 'video_<?php echo $response; ?>';

            function parseWidescreenSize()
            {
                var w = window;
                var d = document;
                var e = d.documentElement;
                var g = d.getElementsByTagName('body')[0];
                var windowWidth = w.innerWidth || e.clientWidth || g.clientWidth;
                var windowHeight = w.innerHeight|| e.clientHeight|| g.clientHeight;
                var videoPlayerElement = document.getElementById(videoPlayerID);

                var aspectRatio = 16/9;   
                var windowRatio = windowWidth / windowHeight;
                var widthVal = windowHeight * aspectRatio; 
                var heightVal = windowWidth / aspectRatio;          

                if(windowRatio > aspectRatio)
                {
                    videoPlayerElement.style.width = widthVal + 'px';
                    videoPlayerElement.style.marginTop = '0px';
                }
                else
                {
                    videoPlayerElement.style.width = windowWidth + 'px';
                    videoPlayerElement.style.marginTop = (windowHeight/2 - heightVal/2) + 'px';
                }
            }

            window.onload = window.onresize = parseWidescreenSize;

            var videoPlayer = videoPlayer || [];
            videoPlayer["video_<?php echo $response; ?>"] = {
                videoID: <?php echo $response; ?>,
                videoWidth: '642',
                videoHeight: '365',
                videoResponsive: true,
                videoPublisher: 'salpr'
                };

        </script>
        <script type="text/javascript" src="http://assets.video.gfrmedia.com/assets/embed/embed-lib.js"></script>
    </body>
</html>