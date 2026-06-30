import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ "output" ]

    connect() {
        // This runs automatically when the element enters the DOM
        console.log("Hello controller connected via AssetMapper!");
    }

    greet() {
        // This runs when the button is clicked
        this.outputTarget.textContent = "Hello, World! 👋";
    }
}
