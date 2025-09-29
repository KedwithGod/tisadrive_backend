import firebase from 'firebase/app';
import 'firebase/messaging';
import axios from 'axios';

let firebaseApp;

export const initializeFirebase = async () => {
    if (!firebase.apps.length) {
        try {
            const response = await axios.get('/firebase/get');
            const firebaseConfig = response.data.settings;
            
            firebaseApp = firebase.initializeApp(firebaseConfig);

        } catch (error) {
            console.error('Error initializing Firebase:', error);
        }
    }
};

export const getFirebaseApp = () => firebaseApp;
