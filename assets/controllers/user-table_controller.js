import { Controller } from '@hotwired/stimulus';
import axios from 'axios';
import {useDispatch} from "stimulus-use";

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

    delete(event){
        event.preventDefault();
        axios.delete(event.params['deleteurl'])
            .then(() => {
                this.dispatch('success');
            })
    }
}
