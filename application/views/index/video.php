
<!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
  <!--  -->  <div id="player"></div>

    <script>
      // 2. This code loads the IFrame Player API code asynchronously.
      var tag = document.createElement('script');

      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      // 3. This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
          height: '390',
          width: '640',
          videoId: 'jQw-hIGiK_0',
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
          }
        });
      }

   // autoplay video
      function onPlayerReady(event) {
         // event.target.playVideo();
      }

      // when video ends
      function onPlayerStateChange(event) {     
         // alert(event);   
          if(event.data === 0) {            
              alert('done');
          }
      }
    </script>