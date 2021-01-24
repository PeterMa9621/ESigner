import { v4 as uuidv4 } from 'uuid';
import fs from 'fs';

export default class FileUtility {
    static getBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
        });
    }

    static saveBase64ToFile(base64, path, extension){
        return new Promise((resolve, reject) => {
            let fileName = uuidv4() + extension;
            let base64File = base64.split(';base64,').pop();
            if(!fs.existsSync(path))
                fs.mkdirSync(path, { recursive: true });
            fs.writeFile(path + fileName, base64File, {encoding: 'base64'}, function(err) {
                if(err === null) {
                    let newUrl = '/api/resources/' + path.split('uploads/')[1] + fileName;
                    resolve(newUrl);
                }
                else
                    reject(err);
            });
        });
    }

    static deleteFile(url) {
        try {
            let pathIndex = url.indexOf("/api/resources/") + ("/api/resources/".length - 1);
            let path = 'uploads/' + url.substring(pathIndex);
            fs.unlinkSync(path);
        } catch (e) {
            console.log(e);
        }
    }
}
