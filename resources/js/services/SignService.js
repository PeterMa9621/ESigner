import axios from "axios";

export default class SignService {
    /**
     * Used to send signature picture to the backend and get the signed document
     * @param id
     * @param data
     * @returns {Promise<any>}
     */
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