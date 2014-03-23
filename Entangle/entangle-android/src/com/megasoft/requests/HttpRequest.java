package com.megasoft.requests;

import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpRequestBase;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;

import android.os.AsyncTask;

public abstract class HttpRequest extends AsyncTask<String, String, String> {
	
	private HttpClient httpClient;
	private HttpRequestBase request;
	
	private boolean hasError = false;
	private String errorMessage = null;
	private boolean hasBody = false;
	
	
	public HttpRequest(){
		httpClient = new DefaultHttpClient();
	}
	
	void setMethod(HttpRequestBase request){
		this.request = request;
	}
	
	public void addHeader(String header,String value){
		this.request.addHeader(header, value);
	}
	
	public boolean hasError() {
		return hasError;
	}

	public String getErrorMessage() {
		return errorMessage;
	}
	
	@Override
	protected String doInBackground(String... args) {
		
		if(hasBody){
			this.request.addHeader("content-type", "application/json");
		}
		
		ResponseHandler<String> handler = new BasicResponseHandler();
    	try{
    		
    		return httpClient.execute(this.request, handler);
    	}catch(Exception e){
    		hasError = true;
    		errorMessage = e.getMessage();
    		return null;
    	}
	}
	
	void setHasBody(boolean hasBody){
		this.hasBody = hasBody;
	}
}