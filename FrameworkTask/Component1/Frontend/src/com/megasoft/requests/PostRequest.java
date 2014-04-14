package com.megasoft.requests;

import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpDelete;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.methods.HttpPut;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;

import android.os.AsyncTask;

public class PostRequest extends Request {
	HttpPost httpPost;
	
	@Override
	protected String doInBackground(String... args) {
		HttpClient httpClient = new DefaultHttpClient();
		this.httpPost = new HttpPost(args[0]);
		this.httpPost.addHeader("content-type", "application/json");
		ResponseHandler<String> handler = new BasicResponseHandler();
    	try{
    		StringEntity data = new StringEntity(args[1]);
    		this.httpPost.setEntity(data);
    		return httpClient.execute(this.httpPost, handler);
    	}catch(Exception e){
    		hasError = true;
    		errorMessage = e.getMessage();
    		return null;
    	}
	}

}
