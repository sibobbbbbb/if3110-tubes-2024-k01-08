class Debouncer {
  constructor(delay = 300) {
    this.delay = delay;
    this.timeoutId = null;
  }

  debounce(callback) {
    return (...args) => {
      if (this.timeoutId) {
        clearTimeout(this.timeoutId);
      }

      this.timeoutId = setTimeout(() => {
        callback.apply(this, args);
      }, this.delay);
    };
  }
}

class NavbarManager {
  constructor() {
    this.bodyElement = document.querySelector("body");

    this.sideBarBlurElement = document.getElementById("sidebar-blur");

    this.sideBarContainerElement = document.getElementById("sidebar-container");

    this.sidebarOpenButton = document.getElementById("sidebar-open-button");

    this.sidebarCloseButton = document.getElementById("sidebar-close-button");

    this.signOutButton = document.getElementById("sign-out-button");

    this.searchForm = document.getElementById("navbar-search-form");

    this.searchInput = document.getElementById("navbar-search-input");

    this.init();
  }

  init() {
    document.addEventListener("DOMContentLoaded", () => {
      // Navbar active links
      this.setupActiveLinksStyle();

      // Event listener
      this.setupEventListeners();
    });
  }

  setupActiveLinksStyle() {
    const currentPath = window.location.pathname;

    const linksElement = Array.from(
      document.querySelectorAll("#navbar-links-ul li a")
    ).filter((linkElement) => !linkElement.querySelector("button"));

    linksElement.forEach((linkElement) => {
      const href = linkElement.getAttribute("href");

      if (currentPath.startsWith(href)) {
        linkElement.classList.add("nav__link--active");
      }
    });
  }

  setupEventListeners() {
    // Open button (menu button)
    this.sidebarOpenButton.addEventListener("click", () => {
      this.onOpenSidebar();
    });

    // Close button
    this.sidebarCloseButton.addEventListener("click", () => {
      this.onCloseSidebar();
    });

    // Sidebar blur
    this.sideBarBlurElement.addEventListener("click", () => {
      this.onCloseSidebar();
    });

    // Search form debounce
    const debouncer = new Debouncer(750);
    this.searchInput.addEventListener(
      "input",
      debouncer.debounce(async () => {
        this.handleDebounceSearch();
      })
    );

    // Sign out button
    if (this.signOutButton) {
      this.signOutButton.addEventListener("click", () => {
        this.handleSignOut();
      });
    }
  }

  async handleDebounceSearch() {
    this.searchForm.submit();
  }

  async handleSignOut() {
    // Initialize ajax
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/auth/sign-out", true);

    xhr.onreadystatechange = () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          window.location.href = "/";
        } else {
          alert(xhr.responseText);
        }

        this.signOutButton.disabled = false;
      }
    };

    // Disable logout buttonl
    this.signOutButton.disabled = true;

    // Send
    xhr.send();
  }

  onOpenSidebar() {
    this.bodyElement.classList.add("body-sidebar-open");

    setTimeout(() => {
      this.sideBarBlurElement.classList.add("sidebar__blur-open");
    }, 100);

    this.sideBarContainerElement.classList.add("sidebar-open");
  }

  onCloseSidebar() {
    this.sideBarContainerElement.classList.remove("sidebar-open");

    setTimeout(() => {
      this.sideBarBlurElement.classList.remove("sidebar__blur-open");
    }, 100);

    this.bodyElement.classList.remove("body-sidebar-open");
  }
}

new NavbarManager();
