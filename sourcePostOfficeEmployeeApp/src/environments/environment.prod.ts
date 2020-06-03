const PHP_API_SERVER = 'http://127.0.0.1:80';
const PHP_API_SERVER_MAMP = 'http://localhost:8888';


/*
brefore ng deploy in terminal ssh -R 80:0.0.0.0:8888 ssh.localhost.run and set url ti environment.url
*/

export const environment = {
  production: true,
  url: '../main'
};
