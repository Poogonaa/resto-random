import { Controller } from '@hotwired/stimulus';
import axios from "axios";

export default class extends Controller {
    static targets = ['content'];
    static values = {
        url: String,
    }

    async refreshContent(event) {
        const response = await axios.get(this.urlValue)
            .then((response) => {
                console.log(response);
                this.contentTarget.innerHTML = response.data;
            });
        //this.contentTarget.innerHTML = await response.text();
    }
}