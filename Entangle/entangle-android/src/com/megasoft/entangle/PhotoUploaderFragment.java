package com.megasoft.entangle;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;

import org.json.JSONException;
import org.json.JSONObject;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.Fragment;
import android.content.ContentResolver;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.Drawable;
import android.net.Uri;
import android.os.Bundle;
import android.util.Base64;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.Toast;

import com.megasoft.requests.PostRequest;

@SuppressLint("NewApi") 
public class PhotoUploaderFragment extends Fragment{
	
	private static final int REQUEST_CODE = 2;
	private static final int RESULT_OK = -1;
	private ImageView icon;
	private Button button;
	private String encodedImage;
	private boolean pickedImage = false;
	
	public void setPickedImage(boolean pickedImage){
		this.pickedImage = pickedImage;
	}
	
	public boolean getPickedImage(){
		return pickedImage;
	}
	
	public void setEncodedImage(String encodedImage){
		this.encodedImage = encodedImage;
		setPickedImage(true);
	}
	
	public static PhotoUploaderFragment getInstance(ContentResolver contentResolver){
		return new PhotoUploaderFragment();
	}
	
	public void setIcon(ImageView icon){
		this.icon = icon;
	}
	
	public void setButton(Button button){
		this.button = button;
	}
	
	public ImageView getIcon(){
		return icon;
	}
	
	public Button getButton(){
		return button;
	}
	
	public String getEncodedImage(){
		return encodedImage;
	}
	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.upload_photo_fragement, container, false);
        
        final ImageView icon = (ImageView) view.findViewById(R.id.icon);
        setIcon(icon);
        
        try{
            InputStream ims = getActivity().getAssets().open("addimage.jpg");
            Drawable d = Drawable.createFromStream(ims, null);
            icon.setImageDrawable(d);
        }
        catch(IOException ex){
        	toasterShow("Error loading page");
        }
        
        final Button iconButton = (Button) view.findViewById(R.id.iconButton);
        setButton(iconButton);
       
        icon.setOnClickListener(new OnClickListener(){
        	public void onClick(View view){
        		chooseIcon();
        	}
        });
        
        iconButton.setOnClickListener(new OnClickListener(){
        	public void onClick(View view){
        		if(getPickedImage()){
	        		iconButton.setClickable(false);
	        		iconButton.setEnabled(false);
	        		
	        		AlertDialog ad = new AlertDialog.Builder(getActivity()).create();
	        		ad.setCancelable(false);
	        		ad.setMessage("Uploading ...");
	        		ad.show();
	        		
	        		sendPhotoData("http://entangletemp.apiary-mock.com/request/1/icon", ad);         
	        		
	        		iconButton.setClickable(true);
	        		iconButton.setEnabled(true);
        		}
        		else{
        			toasterShow("Please Choose an Icon");
        		}
        	}
        });
        
        return view;
	}
	

	public void chooseIcon(){
		startActivityForResult(new Intent(Intent.ACTION_PICK,
				android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI),
				REQUEST_CODE);
	}
	
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		if (resultCode == RESULT_OK && requestCode == REQUEST_CODE
				&& data != null) {
			Bitmap bitmap;
			try{
				bitmap = getPhotoBitmap(data.getData());
			} catch(Exception e){
				toasterShow("Error Fetching Icon");
				return ;
			}
			ImageView imageView = getIcon();
			imageView.setImageBitmap(bitmap);
			ByteArrayOutputStream baos = new ByteArrayOutputStream();
			bitmap.compress(Bitmap.CompressFormat.PNG, 100, baos);
			byte[] byteArray = baos.toByteArray();
			setEncodedImage(Base64.encodeToString(byteArray, Base64.DEFAULT));
		}
	}
	
	public Bitmap getPhotoBitmap(Uri uri) {
		String[] projection = { android.provider.MediaStore.Images.Media.DATA };
		Cursor cursor = getActivity().getContentResolver().query(uri, projection, null, null,
				null);
		int columnIndex = cursor.getColumnIndexOrThrow(projection[0]);
		cursor.moveToFirst();
		String filePath = cursor.getString(columnIndex);
		cursor.close();
		Bitmap bitmap = BitmapFactory.decodeFile(filePath);
		return bitmap;
	}
	public void sendPhotoData(String url, final AlertDialog ad){
		PostRequest iconDataRequest = new PostRequest(url) {
			protected void onPostExecute(String res) {
				String message = "Sorry, there are problems uploading the icon. Please, try again later";
				if (!this.hasError() && res != null) {
					message = "Uploaded!";
				}
				ad.dismiss();
				toasterShow(message);
			}
		};
		JSONObject jsonBody = new JSONObject();
		try {
			jsonBody.put("requestIcon", getEncodedImage());
		} catch (JSONException e) {
			e.printStackTrace();
		}
		iconDataRequest.setBody(jsonBody);
		iconDataRequest.addHeader("X-SESSION-ID", "session1");
		iconDataRequest.execute();
	}
	
	public void toasterShow(String message){
		Toast.makeText(getActivity().getBaseContext(),
				message,
				Toast.LENGTH_LONG).show();
	}
	
	public String getSessionId(){
		return ((UploadRequestIconActivity) getActivity()).getSessionId();
	}
}
