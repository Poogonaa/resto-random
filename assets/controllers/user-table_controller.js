import { Controller } from '@hotwired/stimulus';
import axios from 'axios';
import { useDispatch } from 'stimulus-use';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * user-table_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {
        useDispatch(this);
    }

    edit(event){
        window.location.href = event.params['editurl'];
    }

    active(event){
        event.preventDefault();

        axios.put(event.params['activeurl'])
            .then(() => {
                this.dispatch('success');
            });
    }
}
