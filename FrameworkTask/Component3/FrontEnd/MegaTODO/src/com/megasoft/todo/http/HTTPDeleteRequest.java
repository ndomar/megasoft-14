package com.megasoft.todo.http;

import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpDelete;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;

import android.os.AsyncTask;
import android.util.Log;

public class HTTPDeleteRequest extends AsyncTask<String, String, String> {
	 
	private String rootResource = "http://megatodo.apiary-mock.com";
	
	 public HTTPDeleteRequest (){
		
	}
	
	 public HTTPDeleteRequest (String rootResource){
		 this.rootResource = rootResource;
	}

	@Override
	protected String doInBackground(String... args) {
	    String res = "";
        if (args.length > 0) {
            res = args[0];
        }

        HttpClient httpClient = new DefaultHttpClient();
        HttpDelete httpDel = new HttpDelete(rootResource + res);
        if (args.length > 1) {
        	httpDel.setHeader("X-Session-ID", args[1]);
        }
        ResponseHandler<String> handler = new BasicResponseHandler();
        try{

            return httpClient.execute(httpDel, handler);

        }catch(Exception e ){
            Log.e("matwala3sh", e.toString());
            return "ERROR";
        }
    }


}
