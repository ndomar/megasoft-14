package com.megasoft.requests;

import android.R;
import android.content.Context;
import android.util.Log;
import android.widget.ImageView;

import com.google.android.gms.internal.im;
import com.squareup.picasso.Picasso;

/**
 * The class responsible for viewing the image from the cache if it is there and
 * download it if it's a cache miss.
 * 
 * @author MohamedBassem
 */
public class ImageRequest{
	public ImageRequest(String url,Context context,ImageView imageView){
		if(url.equals("null")){
			int id = context.getResources().getIdentifier("ic_action_person.png", "drawable", context.getPackageName());
			imageView.setImageResource(id);
		}else{
			Picasso.with(context).load(url).into(imageView);
		}
	}
}
