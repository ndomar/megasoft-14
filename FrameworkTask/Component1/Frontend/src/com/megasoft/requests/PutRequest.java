package com.megasoft.requests;

import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpPut;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;

public class PutRequest extends Request {
	HttpPut httpPut;
	
	@Override
	protected String doInBackground(String... args) {
		HttpClient httpClient = new DefaultHttpClient();
		this.httpPut = new HttpPut(args[0]);
		this.httpPut.addHeader("content-type", "application/json");
		ResponseHandler<String> handler = new BasicResponseHandler();
    	try{
    		StringEntity data = new StringEntity(args[1]);
    		this.httpPut.setEntity(data);
    		return httpClient.execute(this.httpPut, handler);
    	}catch(Exception e){
    		hasError = true;
    		errorMessage = e.getMessage();
    		return null;
    	}
	}

}
