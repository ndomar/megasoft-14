package com.megasoft.requests;

import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;

import android.os.AsyncTask;

public class GetRequest extends Request {
	HttpGet httpGet;
	
	@Override
	protected String doInBackground(String... args) {
		HttpClient httpClient = new DefaultHttpClient();
		this.httpGet = new HttpGet(args[0]);
		ResponseHandler<String> handler = new BasicResponseHandler();
    	try{
    		return httpClient.execute(this.httpGet, handler);
    	}catch(Exception e ){
    		hasError = true;
    		errorMessage = e.getMessage();
    		return null;
    	}
	}
	
	

}
