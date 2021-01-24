import axios from "axios";

export default class SignaturePositionService {
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