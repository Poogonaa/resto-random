import { Controller } from '@hotwired/stimulus';
import axios from "axios";

export default class extends Controller {
    static targets = ['content'];
    static values = {
        url: String,
    }

    async refreshContent(event) {
        this.contentTarget.style.opacity = .5;
        const response = await axios.get(this.urlValue)
            .then((response) => {
                console.log(response);
                this.contentTarget.innerHTML = response.data;
                this.contentTarget.style.opacity = 1;
            });
    }
}