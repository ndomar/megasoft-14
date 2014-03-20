package com.megasoft.tweet.http;

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
public class HttpPostRequest extends AsyncTask<String, String, String> {

    private String rootResource = "http://megatweet.apiary.io/";

    public HttpPostRequest() {

    }

    public HttpPostRequest(String rootResource) {
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
