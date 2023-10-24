 <!--notification Toast to show all sorts of notifications, can be called with this: $(".toast").toast('show');-->
 <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 99999999">
    <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <img src="/quizVerwaltung/media/logoQuizManagerTransparent2.png" width="38px" class="rounded me-2" alt="our logo">
        <strong class="me-auto">Quiz Manager</strong>
        <small><?php echo $toastTimeDisplay; ?></small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body" id="toastMsgBody">
       
      </div>
    </div>
  </div>