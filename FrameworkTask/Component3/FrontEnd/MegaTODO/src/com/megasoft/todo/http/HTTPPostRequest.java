package com.megasoft.todo.http;

import android.os.AsyncTask;
import android.util.Log;

import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;

/**
 * Created by mohamedfarghal on 3/15/14.
 */
public class HTTPPostRequest extends AsyncTask<String, String, String> {

    private String rootResource = "http://megatodo.apiary-mock.com";

    public HTTPPostRequest() {

    }

    public HTTPPostRequest(String rootResource) {
        this.rootResource = rootResource;
    }

    @Override
    protected String doInBackground(String... args) {
        String par = "";
        String res = "";
        if (args.length > 0) {
            par = args[0];
        }
        if (args.length > 1) {
            res = args[1];
        }

        HttpClient httpClient = new DefaultHttpClient();
        HttpPost httpPost = new HttpPost(rootResource + res);
        if (args.length > 2) {
        	httpPost.setHeader("X-Session-ID", args[1]);
        }
        httpPost.addHeader("content-type", "application/json");
        ResponseHandler<String> handler = new BasicResponseHandler();
        try{
            StringEntity data = new StringEntity(par);
            httpPost.setEntity(data);
            return httpClient.execute(httpPost, handler);
        }catch(Exception e){
            Log.e("walla3", e.toString());
            return "ERROR";
        }
    }

}
