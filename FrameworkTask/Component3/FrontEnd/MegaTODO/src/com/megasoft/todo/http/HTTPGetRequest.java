package com.megasoft.todo.http;

import android.os.AsyncTask;
import android.util.Log;

import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;

/**
 * Created by mohamedfarghal on 3/15/14.
 */
public class HTTPGetRequest extends AsyncTask<String, String, String> {

    private String rootResource = "http://megatodo.apiary-mock.com";

    public HTTPGetRequest() {

    }

    public HTTPGetRequest(String rootResource) {
        this.rootResource = rootResource;
    }


    @Override
    protected String doInBackground(String... args) {
        String res = "";
        if (args.length > 0) {
            res = args[0];
        }

        HttpClient httpClient = new DefaultHttpClient();
        HttpGet httpGet = new HttpGet(rootResource + res);
        ResponseHandler<String> handler = new BasicResponseHandler();
        try{

            return httpClient.execute(httpGet, handler);

        }catch(Exception e ){
            Log.e("walla3", e.toString());
            return "ERROR";
        }
    }


}
