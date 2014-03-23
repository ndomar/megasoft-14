package com.megasoft.requests;

import java.io.ByteArrayOutputStream;

import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONObject;

import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.util.Base64;
import android.util.Log;

public class PostImageRequest extends Request {
	HttpPost httpPost;
	
	@Override
	protected String doInBackground(String... args	) {
		HttpClient httpClient = new DefaultHttpClient();
		this.httpPost = new HttpPost(args[0]);
		this.httpPost.addHeader("content-type", "application/json");
		ResponseHandler<String> handler = new BasicResponseHandler();
    	try{
    		String imagePath = args[1];
    		Bitmap bm = BitmapFactory.decodeFile(imagePath);
    		ByteArrayOutputStream baos = new ByteArrayOutputStream();  
    		bm.compress(Bitmap.CompressFormat.JPEG, 100, baos);
    		byte[] b = baos.toByteArray(); 
    		String encodedImage = Base64.encodeToString(b, Base64.DEFAULT);
    		JSONObject obj = new JSONObject();
    		obj.put("image", encodedImage );
    		Log.e("test",encodedImage);
    		StringEntity data = new StringEntity(obj.toString());
    		this.httpPost.setEntity(data);
    		return httpClient.execute(this.httpPost, handler);
    	}catch(Exception e){
    		hasError = true;
    		errorMessage = e.getMessage();
    		return null;
    	}
	}

}
