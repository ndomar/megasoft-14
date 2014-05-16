package com.megasoft.requests;

import java.io.IOException;
import java.io.InputStream;
import java.lang.ref.WeakReference;
import java.net.MalformedURLException;
import java.net.URL;

import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.AsyncTask;
import android.widget.ImageView;

/**
 * This code references the second answer from
 * http://stackoverflow.com/questions
 * /15217436/image-view-image-from-imageurl-giving-exception
 */
public class ImageRequest extends AsyncTask<String, Void, Bitmap> {
	private final WeakReference<ImageView> imageViewReference;
	private String data;

	public ImageRequest(ImageView imageView) {
		// Use a WeakReference to ensure the ImageView can be garbage
		// collected
		imageViewReference = new WeakReference<ImageView>(imageView);
	}

	// Decode image in background.
	@Override
	protected Bitmap doInBackground(String... params) {
		data = params[0];
		try {
			if (data == null) {
				return null;
			}
			return BitmapFactory.decodeStream((InputStream) new URL(data)
					.getContent());
		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return null;
	}

	// Once complete, see if ImageView is still around and set bitmap.
	@Override
	protected void onPostExecute(Bitmap bitmap) {
		if (imageViewReference != null && bitmap != null) {
			final ImageView imageView = imageViewReference.get();
			if (imageView != null) {
				imageView.setImageBitmap(bitmap);
			}
		}
	}
}
