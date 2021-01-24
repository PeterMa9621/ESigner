import axios from "axios";

export default class SignService {
    static sign(id, data) {
        return new Promise((resolve, reject) => {
            axios.post('/api/documents/' + id + '/sign', data).then(response => {
                resolve(response.data);
            }).catch(reason => {
                reject(reason);
            });
        });
    }
}