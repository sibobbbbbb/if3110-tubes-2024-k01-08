class QuillEditor {
  constructor(
    containerId,
    formId,
    hiddenInputId,
    placeholder = "Enter your text here..."
  ) {
    this.containerId = containerId;
    this.formId = formId;
    this.hiddenInputId = hiddenInputId;
    this.quill = null;
    this.placeholder = placeholder;
    this.init();
  }

  init() {
    this.quill = new Quill(this.containerId, {
      theme: "snow",
      placeholder: this.placeholder,
    });

    const form = document.getElementById(this.formId);
    form.onsubmit = this.handleSubmit.bind(this);
  }

  handleSubmit() {
    const content = this.quill.root.innerHTML;
    const parsedContent = content.trim() == "<p><br></p>" ? "" : content;
    document.getElementById(this.hiddenInputId).value = parsedContent;
  }

  setContent(content) {
    this.quill.root.innerHTML = content;
  }

  disable() {
    this.quill.disable();
    this.quill.container.classList.add("quill-disabled");
  }

  enable() {
    this.quill.enable();
    this.quill.container.classList.remove("quill-disabled");
  }
}
