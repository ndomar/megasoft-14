package com.megasoft.requests;

import java.io.IOException;

import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpRequestBase;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;

import android.os.AsyncTask;

public abstract class HttpRequest extends AsyncTask<String, String, String> {
	
	/**
	 * The httpClient used for executing requests
	 */
	private HttpClient httpClient;
	
	/**
	 * The request method used
	 */
	private HttpRequestBase request;
	
	/**
	 * See hasError() method
	 */
	private boolean hasError = false;
	
	/**
	 * See getErrorMessage() method
	 */
	private String errorMessage = null;
	
	/**
	 * See setHasBody() method
	 */
	private boolean hasBody = false;
	
	/**
	 * See getStatusCode() method
	 */
	private int statusCode;
	
	
	public HttpRequest(){
		httpClient = new DefaultHttpClient();
	}
	
	/**
	 * This method is called from the subclasses to pass the request method used to this class
	 * @param request , The request class passed from the subclass
	 */
	void setMethod(HttpRequestBase request){
		this.request = request;
	}
	
	/**
	 * Adds a header to the current request
	 * @param header , header key
	 * @param value , header value
	 */
	public void addHeader(String header,String value){
		this.request.addHeader(header, value);
	}
	
	/**
	 * @return false if the status code was anything other than 2XX after executing the request , true otherwise
	 */
	public boolean hasError() {
		return hasError;
	}
	
	/**
	 * A getter for the error message
	 * @return String the error message returned from the request if any
	 */
	public String getErrorMessage() {
		return errorMessage;
	}
	
	/**
	 * This is the method responsible for executing the request and handling the response
	 * @return String , The response body , null in case of errors
	 */
	@Override
	protected String doInBackground(String... args) {
		if(hasBody){
			this.request.addHeader("content-type", "application/json");
		}
		ResponseHandler<String> handler = new BasicResponseHandler();
		HttpResponse x = null;
    	try{
    		x = httpClient.execute(this.request);
    		this.statusCode = x.getStatusLine().getStatusCode();    		
    		return handler.handleResponse(x);
    	}catch(ClientProtocolException  e ){
    		hasError = true;
    		errorMessage = e.getMessage();
    		return null;
    	} catch (IOException e) {
			e.printStackTrace();
			return null;
		}
	}
	
	/**
	 * A getter method for the status code
	 * @return int , the status code of executing the request
	 */
	public int getStatusCode(){
		return this.statusCode;
	}
	
	/**
	 * A setter method to set whether the request has a body or not , used between this class and its subclasses
	 * @param hasBody boolean
	 */
	void setHasBody(boolean hasBody){
		this.hasBody = hasBody;
	}
}