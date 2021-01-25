import axios from "axios";

export default class SignaturePositionService {
    /**
     * Used to send signature position to the backend
     * @param data
     * @returns {Promise<any>}
     */
    static createSignaturePosition(data) {
        return new Promise((resolve, reject) => {
            axios.post('/api/signature_positions', data).then(response => {
                resolve(response.data);
            }).catch(reason => {
                reject(reason);
            });
        });
    }
}