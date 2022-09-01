(function ($) {
  $(document).ready(function () {
    $("#taste-jobs-add-resume-to-apply").length &&
      tasteSetupAddResumeToApplyBtn();

    $("#job_package_selection").length && tasteRestrictMembersOnlyPackages();
  });

  const tasteSetupAddResumeToApplyBtn = () => {
    $("#taste-jobs-add-resume-to-apply").click(function () {
      $("#resume_id").length && $("#resume_id").attr("disabled", true);
    });
  };

  const tasteRestrictMembersOnlyPackages = () => {
    $('li.members-only input[type="radio"]').click(function () {
      alert(
        "You must be a Taste Partner to purchase this Package.\nPlease contact sales@thetaste.ie for more info."
      );
      return false;
    });
  };
})(jQuery);
