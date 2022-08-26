(function ($) {
  $(document).ready(function () {
    $("#taste-jobs-add-resume-to-apply").length &&
      tasteSetupAddResumeToApplyBtn();
  });

  const tasteSetupAddResumeToApplyBtn = () => {
    $("#taste-jobs-add-resume-to-apply").click(function () {
      $("#resume_id").length && $("#resume_id").attr("disabled", true);
    });
  };
})(jQuery);
