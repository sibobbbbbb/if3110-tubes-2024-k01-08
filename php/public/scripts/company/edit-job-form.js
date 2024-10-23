// Initialize quill editor
new QuillEditor(
  "#description-quill-editor",
  "edit-job-form",
  "hidden-description-quill-editor",
  "Fill in the job description"
);

class AttachmentsManager {
  constructor() {
    this.attachments = document.getElementById("form-attachments");
    this.deleteButtons = this.attachments.querySelectorAll(
      ".attachment__delete-button"
    );
    this.submitButton = document.getElementById("edit-job-submit");

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
        this.handleDeleteAttachment(button, event);
      });
    });
  }

  handleDeleteAttachment(currentButton, event) {
    // Disable all delete buttons
    this.deleteButtons.forEach((button) => {
      button.disabled = true;
    });

    // Disable submit button
    this.submitButton.disabled = true;

    // Get the attachment id
    const attachmentId = currentButton.getAttribute("data-attachment-id");

    // Initialize ajax
    const xhr = new XMLHttpRequest();
    xhr.open("DELETE", `/company/jobs/attachments/${attachmentId}`, true);
    xhr.onreadystatechange = async () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          // Remove the attachment element
          currentButton.parentElement.remove();
        } else {
          var jsonResponse = JSON.parse(xhr.responseText);
          alert(jsonResponse.message);
        }

        // Enable all delete buttons
        this.deleteButtons.forEach((button) => {
          button.disabled = false;
        });

        // Enable submit button
        this.submitButton.disabled = false;
      }
    };

    // Send
    xhr.send();
  }
}

new AttachmentsManager();
