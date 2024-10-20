class JobListManager {
  constructor() {
    // jobs li (s)
    this.jobList = document.getElementById("job-list");
    this.deleteButtons = this.jobList.querySelectorAll(
      ".job-list__delete-button"
    );

    this.init();
  }

  init() {
    document.addEventListener("DOMContentLoaded", () => {
      this.setupEventListeners();
    });
  }

  setupEventListeners() {
    this.deleteButtons.forEach((button) => {
      button.addEventListener("click", (event) => {
        this.handleDeleteJob(button, event);
      });
    });
  }

  async handleDeleteJob(currentButton, event) {
    // Disable the button
    currentButton.disabled = true;

    // await new Promise((resolve) => setTimeout(resolve, 10000));

    // Get the job id
    const jobId = currentButton.getAttribute("data-job-id");

    // Initialize ajax
    const xhr = new XMLHttpRequest();
    xhr.open("DELETE", `/company/jobs/${jobId}`, true);
    xhr.onreadystatechange = async () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          // Remove the job element
          currentButton.parentElement.parentElement.parentElement.remove();

          // Enable the button
          currentButton.disabled = false;

          // Reload
          window.location.reload();
        } else {
          var jsonResponse = JSON.parse(xhr.responseText);
          alert(jsonResponse.message);

          // Enable the button
          currentButton.disabled = false;
        }
      }
    };

    xhr.send();
  }
}

const jobListManager = new JobListManager();
